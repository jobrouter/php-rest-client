<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter Client.
 * https://github.com/brotkrueml/jobrouter-client
 *
 * Copyright (c) 2019-2022 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterClient\Tests\Unit\Resource;

use Brotkrueml\JobRouterClient\Exception\InvalidResourceException;
use Brotkrueml\JobRouterClient\Resource\File;
use Brotkrueml\JobRouterClient\Resource\FileInterface;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /**
     * @test
     */
    public function classImplementsFileInterface(): void
    {
        $path = \tempnam('/tmp', 'jrc_');
        \touch($path);

        $subject = new File($path);

        self::assertInstanceOf(FileInterface::class, $subject);

        \unlink($path);
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function constructThrowsExceptionWhenGivenPathDoesNotExist(): void
    {
        $this->expectException(InvalidResourceException::class);
        $this->expectExceptionCode(1582273757);
        $this->expectExceptionMessage('The file "/tmp/not-existing-file.txt" does not exist or is not readable');

        new File('/tmp/not-existing-file.txt');
    }

    /**
     * @test
     */
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
            $subject->toArray()
        );

        \unlink($path);
    }

    /**
     * @test
     */
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
            $subject->toArray()
        );

        \unlink($path);
    }
}
