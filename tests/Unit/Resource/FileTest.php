<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Tests\Unit\Resource;

use Brotkrueml\JobRouterClient\Exception\InvalidResourceException;
use Brotkrueml\JobRouterClient\Resource\File;
use Brotkrueml\JobRouterClient\Resource\FileInterface;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /** @var vfsStreamDirectory */
    private $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup();
    }

    /**
     * @test
     */
    public function classImplementsFileInterface(): void
    {
        $path = $this->root->url() . '/some-file.txt';
        \touch($path);

        $subject = new File($path);

        self::assertInstanceOf(FileInterface::class, $subject);
    }

    /**
     * @test
     */
    public function constructWithOnlyPathThenGettersAreImplementedCorrectly(): void
    {
        $path = $this->root->url() . '/some-file.txt';
        \touch($path);

        $subject = new File($path);

        self::assertSame($path, $subject->getPath());
        self::assertSame('some-file.txt', $subject->getFileName());
        self::assertSame('', $subject->getContentType());
    }

    /**
     * @test
     */
    public function constructWithAllArgumentsThenGettersAreImplementedCorrectly(): void
    {
        $path = $this->root->url() . '/some-file.txt';
        \touch($path);

        $subject = new File($path, 'other-filename.txt', 'foo/bar');

        self::assertSame($path, $subject->getPath());
        self::assertSame('other-filename.txt', $subject->getFileName());
        self::assertSame('foo/bar', $subject->getContentType());
    }

    /**
     * @test
     */
    public function constructThrowsExceptionWhenGivenPathDoesNotExist(): void
    {
        $this->expectException(InvalidResourceException::class);
        $this->expectExceptionCode(1582273757);
        $this->expectExceptionMessage('The file "vfs://root/not-existing-file.txt" does not exist or is not readable');

        new File($this->root->url() . '/not-existing-file.txt');
    }

    /**
     * @test
     */
    public function constructWithOnlyPathThenToArrayIsImplementedCorrectly(): void
    {
        $path = $this->root->url() . '/some-file.txt';
        \touch($path);

        $subject = new File($path);

        self::assertSame(
            [
                'path' => 'vfs://root/some-file.txt',
                'filename' => 'some-file.txt'
            ],
            $subject->toArray()
        );
    }

    /**
     * @test
     */
    public function constructWithAllArgumentsThenToArrayIsImplementedCorrectly(): void
    {
        $path = $this->root->url() . '/some-file.txt';
        \touch($path);

        $subject = new File($path, 'other-filename.txt', 'foo/bar');

        self::assertSame(
            [
                'path' => 'vfs://root/some-file.txt',
                'filename' => 'other-filename.txt',
                'contentType' => 'foo/bar'
            ],
            $subject->toArray()
        );
    }
}
