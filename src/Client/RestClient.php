<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Client;

use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
use Brotkrueml\JobRouterClient\Exception\AuthenticationException;
use Brotkrueml\JobRouterClient\Exception\HttpException;
use Nyholm\Psr7\Stream;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Psr18Client;

/**
 * RestClient for handling HTTP requests
 */
final class RestClient
{
    private const API_ENDPOINT = '/api/rest/v2/';
    private const VERSION = '0.6.0-dev';

    /**
     * @var ClientConfiguration
     * @readonly
     */
    private $configuration;

    /** @var Psr18Client */
    private $psr18Client;

    /** @var string */
    private $jwToken = '';

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

        $client = HttpClient::create([
            'base_uri' => $this->getRestApiUrl(),
            'headers' => ['User-Agent' => $this->getUserAgent()],
        ]);

        $this->psr18Client = new Psr18Client($client);

        $this->authenticate();
    }

    private function getUserAgent(): string
    {
        return \rtrim(
            \sprintf(
                'JobRouterClient/%s (https://github.com/brotkrueml/jobrouter-client) %s',
                static::VERSION,
                $this->configuration->getUserAgentAddition()
            )
        );
    }

    private function getRestApiUrl(): string
    {
        return \rtrim($this->configuration->getBaseUrl(), '/') . self::API_ENDPOINT;
    }

    /**
     * Authenticate against the configured JobRouter system
     *
     * @throws AuthenticationException
     */
    public function authenticate(): void
    {
        $this->jwToken = '';

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
            throw new AuthenticationException(
                \sprintf(
                    'Authentication failed for user "%s" on JobRouter base URL "%s',
                    $this->configuration->getUsername(),
                    $this->configuration->getBaseUrl()
                ),
                1577818398,
                $e
            );
        }

        $content = \json_decode($response->getBody()->getContents(), true);

        if (!isset($content['tokens'][0])) {
            throw new AuthenticationException('Token is unavailable', 1570222016);
        }

        $this->jwToken = $content['tokens'][0];
    }

    /**
     * Send a request to the configured JobRouter system
     *
     * @param string $method The method
     * @param string $route The route
     * @param array $options Additional options for the request (the JobRouter authorization header is added automatically)
     *
     * @return ResponseInterface
     *
     * @throws HttpException
     */
    public function request(string $method, string $route, array $options = []): ResponseInterface
    {
        $route = \ltrim($route, '/');

        $request = $this->psr18Client->createRequest($method, $route);

        if ($this->jwToken) {
            $request = $request->withHeader('x-jobrouter-authorization', 'Bearer ' . $this->jwToken);
        }

        foreach ($options['headers'] ?? [] as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        if (isset($options['json'])) {
            $request = $request->withHeader('content-type', 'application/json');

            if (\is_array($options['json'])) {
                $options['json'] = \json_encode($options['json']);
            }

            if (\is_string($options['json'])) {
                $request = $request->withBody(Stream::create($options['json']));
            }
        }

        $errorMessage = 'Error fetching route ' . $route;
        try {
            $response = $this->psr18Client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new HttpException($errorMessage, 0, $e);
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
}
