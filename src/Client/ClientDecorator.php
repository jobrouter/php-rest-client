<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Client;

use Psr\Http\Message\ResponseInterface;

abstract class ClientDecorator implements ClientInterface
{
    /**
     * @var ClientInterface
     */
    protected $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function authenticate(): void
    {
        $this->client->authenticate();
    }

    public function request(string $method, string $resource, $data = []): ResponseInterface
    {
        return $this->client->request($method, $resource, $data);
    }

    public function getJobRouterVersion(): string
    {
        return $this->client->getJobRouterVersion();
    }
}
