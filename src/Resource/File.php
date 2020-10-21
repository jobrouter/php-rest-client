<?php
declare(strict_types=1);

/**
 * This file is part of the JobRouter Client.
 *
 * Copyright (c) 2019-2020 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 *
 * @see https://github.com/brotkrueml/jobrouter-client
 */

namespace Brotkrueml\JobRouterClient\Resource;

use Brotkrueml\JobRouterClient\Exception\InvalidResourceException;

/**
 * Value object that represents a file
 */
final class File implements FileInterface
{
    /** @var string */
    private $path;

    /** @var string */
    private $fileName;

    /** @var string */
    private $contentType;

    public function __construct(string $path, string $fileName = '', string $contentType = '')
    {
        if (!\file_exists($path)) {
            throw new InvalidResourceException(
                \sprintf(
                    'The file "%s" does not exist or is not readable',
                    $path
                ),
                1582273757
            );
        }

        $this->path = $path;
        $this->fileName = $fileName ?: \basename($path);
        $this->contentType = $contentType;
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
     * @internal
     */
    public function toArray(): array
    {
        $result = [
            'path'=> $this->path,
            'filename' => $this->fileName,
        ];

        if (!empty($this->contentType)) {
            $result['contentType'] = $this->contentType;
        }

        return $result;
    }
}
