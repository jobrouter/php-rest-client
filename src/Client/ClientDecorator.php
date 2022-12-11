<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter Client.
 * https://github.com/brotkrueml/jobrouter-client
 *
 * Copyright (c) 2019-2022 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterClient\Client;

use Psr\Http\Message\ResponseInterface;

abstract class ClientDecorator implements ClientInterface
{
    public function __construct(
        protected readonly ClientInterface $client
    ) {
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
