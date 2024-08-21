<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Exception;

final class FileNotFoundException extends \RuntimeException implements ExceptionInterface
{
    public static function fromEmptyPath(): self
    {
        return new self('No file path given', 1724230703);
    }

    public static function fromPath(string $path, \Throwable $previous): self
    {
        return new self(
            \sprintf(
                'File with path "%s" does not exist',
                $path,
            ),
            1724230704,
            $previous,
        );
    }
}
