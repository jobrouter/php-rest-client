<?php
declare(strict_types = 1);

namespace Unit\Exception;

use Brotkrueml\JobRouterClient\Exception\RestException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class RestExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function givenStandardException(): void
    {
        $exception = new \Exception('standard exception message', 1234);

        $subject = new RestException($exception);

        $this->assertSame('standard exception message', $subject->getMessage());
        $this->assertSame(1234, $subject->getCode());
        $this->assertSame($exception, $subject->getPrevious());
    }

    /**
     * @test
     */
    public function givenTransportException(): void
    {
        $transportException = new TransportException(
            'HTTP/2 500 Internal Server Error',
            500
        );

        $subject = new RestException($transportException);

        $this->assertSame('HTTP/2 500 Internal Server Error', $subject->getMessage());
        $this->assertSame(500, $subject->getCode());
        $this->assertSame($transportException, $subject->getPrevious());
    }

    /**
     * @test
     */
    public function givenClientException(): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock
            ->expects($this->never())
            ->method('getContent');
        $responseMock
            ->expects($this->at(0))
            ->method('getInfo')
            ->with('http_code')
            ->willReturn(404);
        $responseMock
            ->expects($this->at(1))
            ->method('getInfo')
            ->with('url')
            ->willReturn('http://example.org/notfound');
        $responseMock
            ->expects($this->at(2))
            ->method('getInfo')
            ->with('response_headers')
            ->willReturn([]);

        $clientException = new ClientException($responseMock);

        $subject = new RestException($clientException);

        $this->assertSame($clientException, $subject->getPrevious());
    }

    /**
     * @test
     */
    public function givenHttpExceptionInterfaceWithOneError(): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock
            ->expects($this->once())
            ->method('getContent')
            ->with(false)
            ->willReturn('{"errors":{"-":["Authentication failed. Please provide valid credentials and check if the user is not blocked."]}}');

        /** @var MockObject|\Exception $httpExceptionMock */
        $httpExceptionMock = $this->createMock(HttpExceptionInterface::class);
        $httpExceptionMock
            ->expects($this->once())
            ->method('getResponse')
            ->willReturn($responseMock);

        $subject = new RestException($httpExceptionMock);

        $this->assertSame('Authentication failed. Please provide valid credentials and check if the user is not blocked.', $subject->getMessage());
        $this->assertSame(0, $subject->getCode());
        $this->assertSame($httpExceptionMock, $subject->getPrevious());
    }

    /**
     * @test
     */
    public function givenHttpExceptionInterfaceWithTwoErrors(): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock
            ->expects($this->once())
            ->method('getContent')
            ->with(false)
            ->willReturn('{"errors":{"-":["First error.", "Second error."]}}');

        /** @var MockObject|\Exception $httpExceptionMock */
        $httpExceptionMock = $this->createMock(HttpExceptionInterface::class);
        $httpExceptionMock
            ->expects($this->once())
            ->method('getResponse')
            ->willReturn($responseMock);

        $subject = new RestException($httpExceptionMock);

        $this->assertSame('First error. / Second error.', $subject->getMessage());
        $this->assertSame(0, $subject->getCode());
        $this->assertSame($httpExceptionMock, $subject->getPrevious());
    }
}
