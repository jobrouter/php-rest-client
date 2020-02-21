<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Resource;

use Brotkrueml\JobRouterClient\Exception\InvalidResourceException;

/**
 * Value object that represents a file
 * @psalm-immutable
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
