<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Resource;

interface FileInterface
{
    public function __construct(string $path, string $fileName = '', string $contentType = '');

    public function toArray(): array;
}
