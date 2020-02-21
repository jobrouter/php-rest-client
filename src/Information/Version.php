<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Information;

final class Version
{
    private const VERSION = '0.9.0-dev';

    public function getVersion(): string
    {
        return static::VERSION;
    }
}
