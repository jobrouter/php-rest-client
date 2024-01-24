<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Resource;

use JobRouter\AddOn\RestClient\Exception\InvalidResourceException;

/**
 * Value object that represents a file
 */
final class File implements FileInterface
{
    private readonly string $path;
    private readonly string $fileName;

    public function __construct(
        string $path,
        string $fileName = '',
        private readonly string $contentType = '',
    ) {
        if (! \file_exists($path)) {
            throw new InvalidResourceException(
                \sprintf(
                    'The file "%s" does not exist or is not readable',
                    $path,
                ),
                1582273757,
            );
        }

        $this->path = $path;
        $this->fileName = $fileName ?: \basename($path);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return array{path:string, filename:string, contentType?:string}
     * @internal
     */
    public function toArray(): array
    {
        $result = [
            'path' => $this->path,
            'filename' => $this->fileName,
        ];

        if ($this->contentType !== '') {
            $result['contentType'] = $this->contentType;
        }

        return $result;
    }
}
