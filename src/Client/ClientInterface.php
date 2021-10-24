<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter Client.
 * https://github.com/brotkrueml/jobrouter-client
 *
 * Copyright (c) 2019-2021 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterClient\Client;

use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    public function authenticate(): void;

    /**
     * @param array<string,mixed>|ClientDecorator $data
     */
    public function request(string $method, string $resource, $data = []): ResponseInterface;

    public function getJobRouterVersion(): string;
}
