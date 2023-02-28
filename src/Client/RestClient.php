<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter Client.
 * https://github.com/brotkrueml/jobrouter-client
 *
 * Copyright (c) 2019-2023 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterClient\Client;

use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
use Brotkrueml\JobRouterClient\Exception\AuthenticationException;
use Brotkrueml\JobRouterClient\Exception\HttpException;
use Brotkrueml\JobRouterClient\Exception\RestClientException;
use Brotkrueml\JobRouterClient\Mapper\RouteContentTypeMapper;
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
    private readonly Psr17Factory $psr17factory;
    private readonly Browser $browser;
    private readonly AuthorisationMiddleware $authorisationMiddleware;
    private readonly RouteContentTypeMapper $routeContentTypeMapper;
    private string $jobRouterVersion = '';

    /**
     * Creates a RestClient instance, already authenticated against the JobRouter system
     *
     * @param ClientConfiguration $configuration The configuration
     *
     * @throws HttpException
     */
    public function __construct(
        private readonly ClientConfiguration $configuration,
    ) {
        $this->psr17factory = new Psr17Factory();

        $client = new Curl($this->psr17factory, $this->configuration->getClientOptions()->toArray());
        $this->browser = new Browser($client, $this->psr17factory);
        $this->browser->addMiddleware(new UserAgentMiddleware($this->configuration->getUserAgentAddition()));
        $this->authorisationMiddleware = new AuthorisationMiddleware();
        $this->browser->addMiddleware($this->authorisationMiddleware);

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
        $this->authorisationMiddleware->resetToken();

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
            $content = \json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new AuthenticationException('Authorisation response is malformed', 1612552955, $e);
        }

        if (! isset($content['tokens'][0])) {
            throw new AuthenticationException('Token is unavailable', 1570222016);
        }

        $this->authorisationMiddleware->setToken($content['tokens'][0]);

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
                $response = $this->browser->sendRequest($this->buildRequest($method, $resource));
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
     * @param array<string, string|int|bool|FileInterface|array<string|int,mixed>> $multipart
     */
    private function sendForm(string $method, string $resource, array $multipart): ResponseInterface
    {
        \array_walk($multipart, static function (&$value): void {
            if ($value instanceof FileInterface) {
                $value = $value->toArray();
            }
        });

        return $this->browser->submitForm(
            $this->configuration->getJobRouterSystem()->getResourceUrl($resource),
            $multipart,
            $method,
        );
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

        if (\is_string($jsonPayload) && $jsonPayload !== '') {
            $request = $request->withBody($this->psr17factory->createStream($jsonPayload));
        }

        return $this->browser->sendRequest($request);
    }

    private function buildRequest(string $method, string $resource): RequestInterface
    {
        return $this->psr17factory->createRequest(
            $method,
            $this->configuration->getJobRouterSystem()->getResourceUrl($resource),
        );
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
