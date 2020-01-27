<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Information;

final class Version
{
    private const VERSION = '0.8.0';

    public function getVersion(): string
    {
        return static::VERSION;
    }
}
