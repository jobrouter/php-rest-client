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

use JobRouter\AddOn\RestClient\Resource\FileInterface;
use JobRouter\AddOn\RestClient\Resource\FileStorage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FileStorageTest extends TestCase
{
    private FileStorage $subject;

    protected function setUp(): void
    {
        $this->subject = new FileStorage();
    }

    #[Test]
    public function countMethodReturns0WhenNoFilesAreAttached(): void
    {
        self::assertSame(0, $this->subject->count());
    }

    #[Test]
    public function fileStorageImplementsCountableInterface(): void
    {
        self::assertInstanceOf(\Countable::class, $this->subject);
        self::assertCount(0, $this->subject);
    }

    #[Test]
    public function attachAddsFilesToFileStorage(): void
    {
        $fileStub1 = $this->createStub(FileInterface::class);
        $this->subject->attach($fileStub1);

        self::assertTrue($this->subject->contains($fileStub1));
        self::assertCount(1, $this->subject);

        $fileStub2 = $this->createStub(FileInterface::class);
        $this->subject->attach($fileStub2);

        self::assertTrue($this->subject->contains($fileStub2));
        self::assertCount(2, $this->subject);
    }

    #[Test]
    public function attachAddsTheSameFileOnlyOnceToFileStorage(): void
    {
        $fileStub = $this->createStub(FileInterface::class);
        $this->subject->attach($fileStub);

        self::assertCount(1, $this->subject);

        $this->subject->attach($fileStub);

        self::assertCount(1, $this->subject);
    }

    #[Test]
    public function detachRemovesAFileFromFileStorage(): void
    {
        $fileStub = $this->createStub(FileInterface::class);
        $this->subject->attach($fileStub);

        $this->subject->detach($fileStub);

        self::assertFalse($this->subject->contains($fileStub));
        self::assertCount(0, $this->subject);
    }

    #[Test]
    public function currentReturnsFalseWhenFileStorageIsEmpty(): void
    {
        self::assertFalse($this->subject->current());
    }

    #[Test]
    public function validReturnsFalseWhenFileStorageIsEmpty(): void
    {
        self::assertFalse($this->subject->valid());
    }

    #[Test]
    public function fileStorageImplementsIteratorInterface(): void
    {
        self::assertInstanceOf(\Iterator::class, $this->subject);

        $fileStub1 = $this->createStub(FileInterface::class);
        $fileStub2 = $this->createStub(FileInterface::class);
        $fileStub3 = $this->createStub(FileInterface::class);
        $this->subject->attach($fileStub1);
        $this->subject->attach($fileStub2);
        $this->subject->attach($fileStub3);

        self::assertTrue($this->subject->valid());
        self::assertSame($fileStub1, $this->subject->current());
        self::assertSame(\spl_object_hash($fileStub1), $this->subject->key());

        $this->subject->next();

        self::assertTrue($this->subject->valid());
        self::assertSame($fileStub2, $this->subject->current());
        self::assertSame(\spl_object_hash($fileStub2), $this->subject->key());

        $this->subject->next();

        self::assertTrue($this->subject->valid());
        self::assertSame($fileStub3, $this->subject->current());
        self::assertSame(\spl_object_hash($fileStub3), $this->subject->key());

        $this->subject->next();

        self::assertFalse($this->subject->valid());
        self::assertFalse($this->subject->current());

        $this->subject->rewind();

        self::assertTrue($this->subject->valid());
        self::assertSame($fileStub1, $this->subject->current());
    }
}
