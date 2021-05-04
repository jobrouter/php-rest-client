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
