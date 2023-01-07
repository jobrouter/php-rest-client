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

use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;

final class ClientFactory
{
    private function __construct()
    {
        // Class must not be instantiated!
    }

    public static function createRestClient(
        string $baseUrl,
        string $username,
        #[\SensitiveParameter]
        string $password,
        int $lifetime = ClientConfiguration::DEFAULT_TOKEN_LIFETIME_IN_SECONDS,
    ): RestClient {
        $configuration = new ClientConfiguration($baseUrl, $username, $password);
        if ($lifetime !== ClientConfiguration::DEFAULT_TOKEN_LIFETIME_IN_SECONDS) {
            $configuration = $configuration->withLifetime($lifetime);
        }

        return new RestClient($configuration);
    }

    public static function createIncidentsClientDecorator(
        string $baseUrl,
        string $username,
        #[\SensitiveParameter]
        string $password,
        int $lifetime = ClientConfiguration::DEFAULT_TOKEN_LIFETIME_IN_SECONDS,
    ): IncidentsClientDecorator {
        return new IncidentsClientDecorator(static::createRestClient($baseUrl, $username, $password, $lifetime));
    }

    public static function createDocumentsClientDecorator(
        string $baseUrl,
        string $username,
        #[\SensitiveParameter]
        string $password,
        int $lifetime = ClientConfiguration::DEFAULT_TOKEN_LIFETIME_IN_SECONDS,
    ): DocumentsClientDecorator {
        return new DocumentsClientDecorator(static::createRestClient($baseUrl, $username, $password, $lifetime));
    }
}
