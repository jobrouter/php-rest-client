<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Client;

use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
use Brotkrueml\JobRouterClient\Exception\RestClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * RestClient for handling HTTP requests
 */
final class RestClient
{
    private const API_ENDPOINT = '/api/rest/v2/';

    /**
     * @var ClientConfiguration
     * @readonly
     */
    private $configuration;

    /** @var HttpClientInterface */
    private $client;

    /** @var string */
    private $jwToken = '';

    /**
     * Creates a RestClient instance, already authenticated against the JobRouter system
     *
     * @param ClientConfiguration $configuration The configuration
     */
    public function __construct(ClientConfiguration $configuration)
    {
        $this->configuration = $configuration;

        $this->client = HttpClient::create([
            'base_uri' => $this->getRestApiUrl(),
        ]);

        $this->authenticate();
    }

    private function getRestApiUrl(): string
    {
        return \rtrim($this->configuration->getBaseUrl(), '/') . self::API_ENDPOINT;
    }

    /**
     * Authenticate against the configured JobRouter system
     */
    public function authenticate(): void
    {
        $this->jwToken = '';

        $json = [
            'username' => $this->configuration->getUsername(),
            'password' => $this->configuration->getPassword(),
            'lifetime' => $this->configuration->getLifetime(),
        ];

        $response = $this->request('application/tokens', 'POST', ['json' => $json]);

        try {
            $content = $response->toArray();
        } catch (ExceptionInterface $e) {
            throw new RestClientException($e);
        }

        if (!isset($content['tokens'][0])) {
            throw new RestClientException(
                new \RuntimeException('Token unavailable!', 1570222016)
            );
        }

        $this->jwToken = $content['tokens'][0];
    }

    /**
     * Send a request to the configured JobRouter system
     *
     * @param string $route The route
     * @param string $method The method
     * @param array $options Additional options for the request (the JobRouter authorization header is added automatically)
     * @return ResponseInterface
     *
     * @throws RestClientException
     *
     * @see https://github.com/symfony/contracts/blob/master/HttpClient/HttpClientInterface.php Overview of options
     */
    public function request(string $route, string $method = 'GET', array $options = []): ResponseInterface
    {
        $route = \ltrim($route, '/');

        if ($this->jwToken) {
            $options['headers'][] = 'X-Jobrouter-Authorization: Bearer ' . $this->jwToken;
        }

        try {
            return $this->client->request($method, $route, $options);
        } catch (TransportExceptionInterface $e) {
            throw new RestClientException($e);
        }
    }
}
