<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use JobRouter\AddOn\RestClient\Configuration\ClientConfiguration;
use JobRouter\AddOn\RestClient\Exception\AuthenticationException;
use JobRouter\AddOn\RestClient\Exception\HttpException;
use JobRouter\AddOn\RestClient\Exception\RestClientException;
use JobRouter\AddOn\RestClient\Mapper\MultipartFormDataMapper;
use JobRouter\AddOn\RestClient\Mapper\RouteContentTypeMapper;
use JobRouter\AddOn\RestClient\Middleware\AuthorisationMiddleware;
use JobRouter\AddOn\RestClient\Middleware\UserAgentMiddleware;
use JobRouter\AddOn\RestClient\Resource\FileInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * RestClient for handling HTTP requests to a JobRouter instance
 */
final class RestClient implements ClientInterface
{
    private readonly Client $client;
    private readonly RouteContentTypeMapper $routeContentTypeMapper;
    private readonly bool $useNtlm;
    private string $jobRouterVersion = '';
    private string $authorisationToken = '';

    public function __construct(
        private readonly ClientConfiguration $configuration,
    ) {
        $this->useNtlm = $this->configuration->getUseNtlm();

        $stack = HandlerStack::create();
        // CurlHandler is necessary to use NTLM
        $stack->setHandler(new CurlHandler());
        $stack->push((new UserAgentMiddleware())($this->configuration->getUserAgentAddition()));
        if (! $this->useNtlm) {
            $stack->push((new AuthorisationMiddleware())($this->authorisationToken));
        }

        $options = [
            ...$this->configuration->getClientOptions()->toArray(),
            ...[
                'base_uri' => $configuration->getJobRouterSystem()->getBaseUrl(),
                'handler' => $stack,
                'synchronous' => true,
            ],
        ];
        if ($this->useNtlm) {
            $options['auth'] = ['', '', 'ntlm'];
        }

        $this->client = new Client($options);

        $this->routeContentTypeMapper = new RouteContentTypeMapper();
    }

    /**
     * Authenticate against the configured JobRouter system
     *
     * @throws AuthenticationException
     * @throws HttpException
     */
    public function authenticate(): self
    {
        if ($this->useNtlm) {
            throw AuthenticationException::fromActivatedNtlm();
        }

        $this->authorisationToken = '';

        $options = [
            'username' => $this->configuration->getUsername(),
            'password' => $this->configuration->getPassword(),
            'lifetime' => $this->configuration->getLifetime(),
        ];

        try {
            $response = $this->request('POST', 'application/tokens', $options);
        } catch (HttpException $e) {
            if ($e->getCode() === 401) {
                throw AuthenticationException::fromFailedAuthentication($this->configuration, 1577818398, $e);
            }

            throw $e;
        }

        $this->detectJobRouterVersionFromResponse($response);

        try {
            $content = \json_decode($response->getBody()->getContents(), true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new AuthenticationException('Authorisation response is malformed', 1612552955, $e);
        }

        if (! isset($content['tokens'][0])) {
            throw new AuthenticationException('Token is unavailable', 1570222016);
        }

        $this->authorisationToken = $content['tokens'][0];

        return $this;
    }

    private function detectJobRouterVersionFromResponse(ResponseInterface $response): void
    {
        $this->jobRouterVersion = $response->getHeaderLine('x-jobrouter-version');
    }

    /**
     * Send a request to the configured JobRouter system
     *
     * @param string $method The method
     * @param string $resource The resource path
     * @param array<string,mixed> $data Data for the request
     *
     * @throws HttpException
     */
    public function request(string $method, string $resource, $data = []): ResponseInterface
    {
        if (! \is_array($data)) {
            throw new RestClientException(
                \sprintf(
                    'data must be an array, "%s" given',
                    \get_debug_type($data),
                ),
                1578233543,
            );
        }

        $resource = \ltrim($resource, '/');
        $contentType = $this->routeContentTypeMapper->getRequestContentTypeForRoute($method, $resource);

        try {
            if ($contentType === 'multipart/form-data') {
                $response = $this->sendForm($method, $resource, $data);
            } elseif ($contentType === 'application/json') {
                $response = $this->sendJson($method, $resource, $data);
            } else {
                $response = $this->client->sendRequest($this->buildRequest($method, $resource));
            }
        } catch (ClientExceptionInterface $e) {
            throw HttpException::fromError(
                $e->getCode(),
                $this->configuration->getJobRouterSystem()->getResourceUrl($resource),
                $e->getMessage(),
                $e,
            );
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode >= 400) {
            throw HttpException::fromError(
                $statusCode,
                $this->configuration->getJobRouterSystem()->getResourceUrl($resource),
                $response->getBody()->getContents(),
            );
        }
        if ($statusCode >= 300) {
            throw HttpException::fromRedirect(
                $statusCode,
                $this->configuration->getJobRouterSystem()->getResourceUrl($resource),
                $response->getHeaderLine('location'),
            );
        }

        return $response;
    }

    /**
     * @param array<string, string|int|float|bool|FileInterface|array{path: non-empty-string, filename?: string, contentType?: string}> $data
     * @throws GuzzleException
     */
    private function sendForm(string $method, string $resource, array $data): ResponseInterface
    {
        $multipart = (new MultipartFormDataMapper())->map($data);
        $request = $this->buildRequest($method, $resource);

        return $this->client->send($request, [
            'multipart' => $multipart,
        ]);
    }

    /**
     * @param string|array<string, mixed> $jsonPayload
     */
    private function sendJson(string $method, string $resource, string|array $jsonPayload): ResponseInterface
    {
        $request = $this->buildRequest($method, $resource);
        $request = $request->withHeader('content-type', 'application/json');

        if (\is_array($jsonPayload)) {
            $jsonPayload = \json_encode($jsonPayload, \JSON_THROW_ON_ERROR);
        }

        if ($jsonPayload !== '') {
            $request = $request->withBody(Utils::streamFor($jsonPayload));
        }

        return $this->client->sendRequest($request);
    }

    private function buildRequest(string $method, string $resource): RequestInterface
    {
        return new Request($method, $this->configuration->getJobRouterSystem()->getResourceUrl($resource));
    }

    /**
     * Get the version number of the JobRouter installation
     * The version is available after calling the authenticate method,
     * directly after instantiation an empty string is returned.
     */
    public function getJobRouterVersion(): string
    {
        return $this->jobRouterVersion;
    }
}
