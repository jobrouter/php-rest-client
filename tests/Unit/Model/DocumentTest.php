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

namespace Brotkrueml\JobRouterClient\Tests\Unit\Model;

use Brotkrueml\JobRouterClient\Model\Document;
use Brotkrueml\JobRouterClient\Resource\FileInterface;
use Brotkrueml\JobRouterClient\Resource\FileStorage;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    /**
     * @var Document
     */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new Document();
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function getIndexFieldReturnsNullWhenNotExisting(): void
    {
        self::assertNull($this->subject->getIndexField('notExisting'));
    }

    /**
     * @test
     */
    public function setIndexFieldReturnsSelf(): void
    {
        self::assertSame($this->subject, $this->subject->setIndexField('someIndexField', 'some value'));
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function getKeywordFieldReturnsNullWhenNotExisting(): void
    {
        self::assertNull($this->subject->getKeywordField('notExisting'));
    }

    /**
     * @test
     */
    public function setKeywordFieldReturnsSelf(): void
    {
        self::assertSame($this->subject, $this->subject->setKeywordField('someKeywordField', 'some value'));
    }

    /**
     * @test
     */
    public function setAndGetFiles(): void
    {
        self::assertCount(0, $this->subject->getFiles());

        $fileStub = $this->createStub(FileInterface::class);
        $this->subject->addFile($fileStub);

        $actual = $this->subject->getFiles();
        self::assertCount(1, $actual);
        self::assertTrue($actual->contains($fileStub));
    }

    /**
     * @test
     */
    public function setFiles(): void
    {
        $fileStorage = new FileStorage();
        $this->subject->setFiles($fileStorage);

        self::assertSame($fileStorage, $this->subject->getFiles());
    }

    /**
     * @test
     */
    public function setFilesReturnsSelf(): void
    {
        self::assertSame($this->subject, $this->subject->setFiles(new FileStorage()));
    }
}
