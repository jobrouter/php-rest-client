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

interface ClientInterface
{
    public function authenticate(): self;

    /**
     * @param array<string,mixed>|ClientDecorator $data
     */
    public function request(string $method, string $resource, $data = []): ResponseInterface;

    public function getJobRouterVersion(): string;
}
