<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Tests\Unit\Client;

use JobRouter\AddOn\RestClient\Client\ClientInterface;
use JobRouter\AddOn\RestClient\Client\DocumentsClientDecorator;
use JobRouter\AddOn\RestClient\Model\Document;
use JobRouter\AddOn\RestClient\Resource\FileInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class DocumentsClientDecoratorTest extends TestCase
{
    private ClientInterface&MockObject $clientMock;
    private DocumentsClientDecorator $subject;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);

        $this->subject = new DocumentsClientDecorator($this->clientMock);
    }

    #[Test]
    public function requestIsPassedUnchangedToClientIfArrayIsGivenAsDataAndReturnsInstanceOfResponseInterface(): void
    {
        $responseStub = $this->createStub(ResponseInterface::class);

        $this->clientMock
            ->expects(self::once())
            ->method('request')
            ->with('GET', 'some/route', [
                'some' => 'data',
            ])
            ->willReturn($responseStub);

        $actual = $this->subject->request('GET', 'some/route', [
            'some' => 'data',
        ]);

        self::assertInstanceOf(ResponseInterface::class, $actual);
    }

    #[Test]
    public function requestWithDocumentIsProcessedAsMultipartAndPassedToClient(): void
    {
        $fileStub1 = $this->createStub(FileInterface::class);
        $fileStub2 = $this->createStub(FileInterface::class);

        $document = (new Document())
            ->setIndexField('someIndexField', 'some index value')
            ->setIndexField('anotherIndexField', 'another index value')
            ->setKeywordField('someKeywordField', 'some keyword value')
            ->setKeywordField('anotherKeywordField', 'another keyword value')
            ->addFile($fileStub1)
            ->addFile($fileStub2);

        $multipart = [
            'indexFields[0][name]' => 'someIndexField',
            'indexFields[0][value]' => 'some index value',
            'indexFields[1][name]' => 'anotherIndexField',
            'indexFields[1][value]' => 'another index value',
            'keywordFields[0][name]' => 'someKeywordField',
            'keywordFields[0][value]' => 'some keyword value',
            'keywordFields[1][name]' => 'anotherKeywordField',
            'keywordFields[1][value]' => 'another keyword value',
            'files[0]' => $fileStub1,
            'files[1]' => $fileStub2,
        ];

        $this->clientMock
            ->expects(self::once())
            ->method('request')
            ->with('POST', 'some/route', $multipart);

        $this->subject->request('POST', 'some/route', $document);
    }

    #[Test]
    public function requestWithDocumentIsProcessedAndReturnsInstanceOfResponseInterface(): void
    {
        $responseStub = $this->createStub(ResponseInterface::class);

        $this->clientMock
            ->expects(self::once())
            ->method('request')
            ->willReturn($responseStub);

        $actual = $this->subject->request('GET', 'some/route', new Document());

        self::assertInstanceOf(ResponseInterface::class, $actual);
    }

    #[Test]
    public function requestWithEmptyDocumentHasEmptyMultipart(): void
    {
        $this->clientMock
            ->expects(self::once())
            ->method('request')
            ->with('POST', 'some/route', []);

        $this->subject->request('POST', 'some/route', new Document());
    }
}
