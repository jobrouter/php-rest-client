<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Tests\Unit\Model;

use JobRouter\AddOn\RestClient\Model\Document;
use JobRouter\AddOn\RestClient\Resource\FileInterface;
use JobRouter\AddOn\RestClient\Resource\FileStorage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    private Document $subject;

    protected function setUp(): void
    {
        $this->subject = new Document();
    }

    #[Test]
    public function setAndGetIndexField(): void
    {
        self::assertCount(0, $this->subject->getIndexFields());

        $this->subject->setIndexField('someIndexField', 'some value');

        self::assertCount(1, $this->subject->getIndexFields());
        self::assertSame('some value', $this->subject->getIndexField('someIndexField'));

        $this->subject->setIndexField('anotherIndexField', 'another value');

        self::assertCount(2, $this->subject->getIndexFields());
        self::assertSame('another value', $this->subject->getIndexField('anotherIndexField'));
    }

    #[Test]
    public function getIndexFieldReturnsNullWhenNotExisting(): void
    {
        self::assertNull($this->subject->getIndexField('notExisting'));
    }

    #[Test]
    public function setIndexFieldReturnsSelf(): void
    {
        self::assertSame($this->subject, $this->subject->setIndexField('someIndexField', 'some value'));
    }

    #[Test]
    public function setAndGetKeywordField(): void
    {
        self::assertCount(0, $this->subject->getKeywordFields());

        $this->subject->setKeywordField('someKeywordField', 'some value');

        self::assertCount(1, $this->subject->getKeywordFields());
        self::assertSame('some value', $this->subject->getKeywordField('someKeywordField'));

        $this->subject->setKeywordField('anotherKeywordField', 'another value');

        self::assertCount(2, $this->subject->getKeywordFields());
        self::assertSame('another value', $this->subject->getKeywordField('anotherKeywordField'));
    }

    #[Test]
    public function getKeywordFieldReturnsNullWhenNotExisting(): void
    {
        self::assertNull($this->subject->getKeywordField('notExisting'));
    }

    #[Test]
    public function setKeywordFieldReturnsSelf(): void
    {
        self::assertSame($this->subject, $this->subject->setKeywordField('someKeywordField', 'some value'));
    }

    #[Test]
    public function setAndGetFiles(): void
    {
        self::assertCount(0, $this->subject->getFiles());

        $fileStub = $this->createStub(FileInterface::class);
        $this->subject->addFile($fileStub);

        $actual = $this->subject->getFiles();
        self::assertCount(1, $actual);
        self::assertTrue($actual->contains($fileStub));
    }

    #[Test]
    public function setFiles(): void
    {
        $fileStorage = new FileStorage();
        $this->subject->setFiles($fileStorage);

        self::assertSame($fileStorage, $this->subject->getFiles());
    }

    #[Test]
    public function setFilesReturnsSelf(): void
    {
        self::assertSame($this->subject, $this->subject->setFiles(new FileStorage()));
    }
}
