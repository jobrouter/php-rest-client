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

use Psr\Http\Message\ResponseInterface;

abstract class ClientDecorator implements ClientInterface
{
    public function __construct(
        protected readonly ClientInterface $client,
    ) {}

    public function authenticate(): self
    {
        $this->client->authenticate();

        return $this;
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
