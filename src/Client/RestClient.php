<?php
declare(strict_types = 1);

namespace Brotkrueml\JobRouterClient\Client;

use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
use Brotkrueml\JobRouterClient\Exception\RestException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class RestClient
{
    /** @var ClientConfiguration */
    protected $configuration;

    /** @var HttpClientInterface */
    protected $client;

    private $jwToken = '';

    /**
     * @param ClientConfiguration $configuration
     * @param MockHttpClient|null $client (only for testing purposes!)
     */
    public function __construct(ClientConfiguration $configuration, MockHttpClient $client = null)
    {
        $this->configuration = $configuration;

        if ($client) {
            $this->client = $client;
        } else {
            $this->client = HttpClient::create([
                'base_uri' => $this->configuration->getRestApiUri(),
            ]);
        }

        $this->authenticate();
    }

    /**
     * Authenticate against the configured JobRouter system
     */
    public function authenticate(): void
    {
        $this->jwToken = null;

        $json = [
            'username' => $this->configuration->getUsername(),
            'password' => $this->configuration->getPassword(),
            'lifetime' => $this->configuration->getLifetime(),
        ];

        $response = $this->request('application/tokens', 'POST', ['json' => $json]);

        try {
            $content = $response->toArray();
        } catch (\Throwable $e) {
            throw new RestException($e->getMessage(), 1565710927, $e);
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
            throw new RestException($e->getMessage(), 1565710963, $e);
        }
    }
}
