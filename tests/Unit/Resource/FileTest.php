<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Tests\Unit\Resource;

use JobRouter\AddOn\RestClient\Exception\InvalidResourceException;
use JobRouter\AddOn\RestClient\Resource\File;
use JobRouter\AddOn\RestClient\Resource\FileInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    #[Test]
    public function classImplementsFileInterface(): void
    {
        $path = \tempnam('/tmp', 'jrc_');
        \touch($path);

        $subject = new File($path);

        self::assertInstanceOf(FileInterface::class, $subject);

        \unlink($path);
    }

    #[Test]
    public function constructWithOnlyPathThenGettersAreImplementedCorrectly(): void
    {
        $path = \tempnam('/tmp', 'jrc_');
        \touch($path);

        $subject = new File($path);

        self::assertSame($path, $subject->getPath());
        self::assertSame(\basename($path), $subject->getFileName());
        self::assertSame('', $subject->getContentType());

        \unlink($path);
    }

    #[Test]
    public function constructWithAllArgumentsThenGettersAreImplementedCorrectly(): void
    {
        $path = \tempnam('/tmp', 'jrc_');
        \touch($path);

        $subject = new File($path, 'other-filename.txt', 'foo/bar');

        self::assertSame($path, $subject->getPath());
        self::assertSame('other-filename.txt', $subject->getFileName());
        self::assertSame('foo/bar', $subject->getContentType());

        \unlink($path);
    }

    #[Test]
    public function constructThrowsExceptionWhenGivenPathDoesNotExist(): void
    {
        $this->expectException(InvalidResourceException::class);
        $this->expectExceptionCode(1582273757);
        $this->expectExceptionMessage('The file "/tmp/not-existing-file.txt" does not exist or is not readable');

        new File('/tmp/not-existing-file.txt');
    }

    #[Test]
    public function constructWithOnlyPathThenToArrayIsImplementedCorrectly(): void
    {
        $path = \tempnam('/tmp', 'jrc_');
        \touch($path);

        $subject = new File($path);

        self::assertSame(
            [
                'path' => $path,
                'filename' => \basename($path),
            ],
            $subject->toArray(),
        );

        \unlink($path);
    }

    #[Test]
    public function constructWithAllArgumentsThenToArrayIsImplementedCorrectly(): void
    {
        $path = \tempnam('/tmp', 'jrc_');
        \touch($path);

        $subject = new File($path, 'other-filename.txt', 'foo/bar');

        self::assertSame(
            [
                'path' => $path,
                'filename' => 'other-filename.txt',
                'contentType' => 'foo/bar',
            ],
            $subject->toArray(),
        );

        \unlink($path);
    }
}
