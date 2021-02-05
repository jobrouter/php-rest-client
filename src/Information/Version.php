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

namespace Brotkrueml\JobRouterClient\Information;

/**
 * Value object that holds the current version of the library
 * @psalm-immutable
 */
final class Version
{
    private const VERSION = '1.1.0-dev';

    public function getVersion(): string
    {
        return static::VERSION;
    }
}
