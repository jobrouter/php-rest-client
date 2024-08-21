<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Tests\Unit\Exception;

use JobRouter\AddOn\RestClient\Exception\FileNotFoundException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FileNotFoundExceptionTest extends TestCase
{
    #[Test]
    public function fromEmptyPath(): void
    {
        $actual = FileNotFoundException::fromEmptyPath();

        self::assertSame('No file path given', $actual->getMessage());
        self::assertSame(1724230703, $actual->getCode());
    }

    #[Test]
    public function fromPath(): void
    {
        $previous = new \Exception();

        $actual = FileNotFoundException::fromPath('/some/path.txt', $previous);

        self::assertSame('File with path "/some/path.txt" does not exist', $actual->getMessage());
        self::assertSame(1724230704, $actual->getCode());
        self::assertSame($previous, $actual->getPrevious());
    }
}
