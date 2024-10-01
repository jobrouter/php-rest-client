<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Information;

/**
 * Value object that holds the current version of the library
 */
final class Version
{
    private const VERSION = '3.1.0';

    public function getVersion(): string
    {
        return self::VERSION;
    }
}
