<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Client;

use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
use Brotkrueml\JobRouterClient\Exception\AuthenticationException;
use Brotkrueml\JobRouterClient\Exception\HttpException;
use Brotkrueml\JobRouterClient\Exception\RestClientException;
use Brotkrueml\JobRouterClient\Middleware\AuthorisationMiddleware;
use Brotkrueml\JobRouterClient\Middleware\UserAgentMiddleware;
use Brotkrueml\JobRouterClient\Resource\FileInterface;
use Buzz\Browser;
use Buzz\Client\Curl;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * RestClient for handling HTTP requests
 */
final class RestClient implements ClientInterface
{
    private const API_ENDPOINT = '/api/rest/v2/';

    /**
     * @var ClientConfiguration
     * @readonly
     */
    private $configuration;

    /** @var Psr17Factory */
    private $psr17factory;

    /** @var Browser */
    private $browser;

    /** @var AuthorisationMiddleware */
    private $authorisationMiddleware;

    /**
     * Creates a RestClient instance, already authenticated against the JobRouter system
     *
     * @param ClientConfiguration $configuration The configuration
     *
     * @throws AuthenticationException
     * @throws HttpException
     */
    public function __construct(ClientConfiguration $configuration)
    {
        $this->configuration = $configuration;
        $this->psr17factory = new Psr17Factory();

        $client = new Curl($this->psr17factory);
        $this->browser = new Browser($client, $this->psr17factory);
        $this->browser->addMiddleware(new UserAgentMiddleware($this->configuration->getUserAgentAddition()));
        $this->authorisationMiddleware = new AuthorisationMiddleware();
        $this->browser->addMiddleware($this->authorisationMiddleware);

        $this->authenticate();
    }

    /**
     * Authenticate against the configured JobRouter system
     *
     * @throws AuthenticationException
     */
    public function authenticate(): void
    {
        $this->authorisationMiddleware->resetToken();

        $options = [
            'json' => [
                'username' => $this->configuration->getUsername(),
                'password' => $this->configuration->getPassword(),
                'lifetime' => $this->configuration->getLifetime(),
            ],
        ];

        try {
            $response = $this->request('POST', 'application/tokens', $options);
        } catch (HttpException $e) {
            throw AuthenticationException::fromFailedAuthentication($this->configuration, 1577818398, $e);
        }

        $content = \json_decode($response->getBody()->getContents(), true);

        if (!isset($content['tokens'][0])) {
            throw new AuthenticationException('Token is unavailable', 1570222016);
        }

        $this->authorisationMiddleware->setToken($content['tokens'][0]);
    }

    /**
     * Send a request to the configured JobRouter system
     *
     * @param string $method The method
     * @param string $resource The resource path
     * @param array $data Data for the request with key 'json' for json data or key 'multipart' for multipart/form-data
     *
     * @return ResponseInterface
     *
     * @throws HttpException
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     * @psalm-suppress DocblockTypeContradiction
     * @noRector \Rector\TypeDeclaration\Rector\FunctionLike\ParamTypeDeclarationRector
     */
    public function request(string $method, string $resource, $data = []): ResponseInterface
    {
        if (!\is_array($data)) {
            throw new RestClientException(
                \sprintf(
                    'data must be an array, "%s" given',
                    gettype($data)
                ),
                1578233543
            );
        }

        $errorMessage = 'Error fetching route ' . $resource;

        try {
            if (isset($data['multipart'])) {
                $response = $this->sendForm($method, $resource, $data['multipart']);
            } elseif (isset($data['json'])) {
                $response = $this->sendJson($method, $resource, $data['json']);
            } else {
                $response = $this->browser->sendRequest($this->buildRequest($method, $resource));
            }
        } catch (ClientExceptionInterface $e) {
            throw new HttpException($errorMessage, (int)$e->getCode(), $e);
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode >= 400) {
            $content = \json_decode($response->getBody()->getContents(), true);
            if (isset($content['errors']['-'][0])) {
                $errorMessage .= ': ' . $content['errors']['-'][0];
            }

            throw new HttpException($errorMessage, $statusCode);
        }

        return $response;
    }

    private function sendForm(string $method, string $resource, array $multipart): ResponseInterface
    {
        /** @psalm-suppress MissingClosureParamType */
        \array_walk($multipart, function(&$value): void {
            if ($value instanceof FileInterface) {
                $value = $value->toArray();
            }
        });

        return $this->browser->submitForm(
            $this->getFullResourceUrl($resource),
            $multipart,
            $method
        );
    }

    private function getFullResourceUrl(string $resource): string
    {
        return \rtrim($this->configuration->getBaseUrl(), '/')
            . self::API_ENDPOINT
            . \ltrim($resource, '/');
    }

    /**
     * @param string $method
     * @param string $resource
     * @param string|array $jsonPayload
     * @return ResponseInterface
     */
    private function sendJson(string $method, string $resource, $jsonPayload): ResponseInterface
    {
        $request = $this->buildRequest($method, $resource);
        $request = $request->withHeader('content-type', 'application/json');

        if (\is_array($jsonPayload)) {
            $jsonPayload = \json_encode($jsonPayload);
        }

        if (\is_string($jsonPayload) && !empty($jsonPayload)) {
            $request = $request->withBody($this->psr17factory->createStream($jsonPayload));
        }

        return $this->browser->sendRequest($request);
    }

    private function buildRequest(string $method, string $resource): RequestInterface
    {
        return $this->psr17factory->createRequest($method, $this->getFullResourceUrl($resource));
    }
}
