<?php
declare(strict_types=1);

/**
 * This file is part of the JobRouter Client.
 *
 * Copyright (c) 2019-2021 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 *
 * @see https://github.com/brotkrueml/jobrouter-client
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

    /** @var RouteContentTypeMapper */
    private $routeContentTypeMapper;

    /** @var string */
    private $jobRouterVersion = '';

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

        $this->routeContentTypeMapper = new RouteContentTypeMapper();

        $this->authenticate();
    }

    /**
     * Authenticate against the configured JobRouter system
     *
     * @throws AuthenticationException
     * @throws HttpException
     */
    public function authenticate(): void
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
            $content = (array)\json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new AuthenticationException('Authorisation response is malformed', 1612552955, $e);
        }

        if (!isset($content['tokens'][0])) {
            throw new AuthenticationException('Token is unavailable', 1570222016);
        }

        $this->authorisationMiddleware->setToken($content['tokens'][0]);
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
     * @param array $data Data for the request
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
                    \get_debug_type($data)
                ),
                1578233543
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
                (int)$e->getCode(),
                $this->configuration->getJobRouterSystem()->getResourceUrl($resource),
                $e->getMessage(),
                $e
            );
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode >= 400) {
            throw HttpException::fromError(
                $statusCode,
                $this->configuration->getJobRouterSystem()->getResourceUrl($resource),
                $response->getBody()->getContents()
            );
        }
        if ($statusCode >= 300) {
            throw HttpException::fromRedirect(
                $statusCode,
                $this->configuration->getJobRouterSystem()->getResourceUrl($resource),
                $response->getHeaderLine('location')
            );
        }

        return $response;
    }

    private function sendForm(string $method, string $resource, array $multipart): ResponseInterface
    {
        /** @psalm-suppress MissingClosureParamType */
        \array_walk($multipart, static function (&$value): void {
            if ($value instanceof FileInterface) {
                $value = $value->toArray();
            }
        });

        return $this->browser->submitForm(
            $this->configuration->getJobRouterSystem()->getResourceUrl($resource),
            $multipart,
            $method
        );
    }

    /**
     * @param string|array $jsonPayload
     */
    private function sendJson(string $method, string $resource, $jsonPayload): ResponseInterface
    {
        $request = $this->buildRequest($method, $resource);
        $request = $request->withHeader('content-type', 'application/json');

        if (\is_array($jsonPayload)) {
            $jsonPayload = \json_encode($jsonPayload, \JSON_THROW_ON_ERROR);
        }

        if (\is_string($jsonPayload) && !empty($jsonPayload)) {
            $request = $request->withBody($this->psr17factory->createStream($jsonPayload));
        }

        return $this->browser->sendRequest($request);
    }

    private function buildRequest(string $method, string $resource): RequestInterface
    {
        return $this->psr17factory->createRequest(
            $method,
            $this->configuration->getJobRouterSystem()->getResourceUrl($resource)
        );
    }

    /**
     * Get the version number of the JobRouter installation
     */
    public function getJobRouterVersion(): string
    {
        return $this->jobRouterVersion;
    }
}
