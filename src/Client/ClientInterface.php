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

use Brotkrueml\JobRouterClient\Model\Document;
use Brotkrueml\JobRouterClient\Model\Incident;
use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    public function authenticate(): void;

    /**
     * @param array|Incident|Document $data
     */
    public function request(string $method, string $resource, $data = []): ResponseInterface;

    public function getJobRouterVersion(): string;
}
