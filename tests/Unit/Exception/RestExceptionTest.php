<?php
declare(strict_types=1);

namespace Unit\Exception;

use Brotkrueml\JobRouterClient\Exception\RestClientException;
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

        $subject = new RestClientException($exception);

        self::assertSame('standard exception message', $subject->getMessage());
        self::assertSame(1234, $subject->getCode());
        self::assertSame($exception, $subject->getPrevious());
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

        $subject = new RestClientException($transportException);

        self::assertSame('HTTP/2 500 Internal Server Error', $subject->getMessage());
        self::assertSame(500, $subject->getCode());
        self::assertSame($transportException, $subject->getPrevious());
    }

    /**
     * @test
     */
    public function givenClientException(): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock
            ->expects(self::never())
            ->method('getContent');
        $responseMock
            ->expects(self::at(0))
            ->method('getInfo')
            ->with('http_code')
            ->willReturn(404);
        $responseMock
            ->expects(self::at(1))
            ->method('getInfo')
            ->with('url')
            ->willReturn('http://example.org/notfound');
        $responseMock
            ->expects(self::at(2))
            ->method('getInfo')
            ->with('response_headers')
            ->willReturn([]);

        $clientException = new ClientException($responseMock);

        $subject = new RestClientException($clientException);

        self::assertSame($clientException, $subject->getPrevious());
    }

    /**
     * @test
     */
    public function givenHttpExceptionInterfaceWithOneError(): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock
            ->expects(self::once())
            ->method('getContent')
            ->with(false)
            ->willReturn('{"errors":{"-":["Authentication failed. Please provide valid credentials and check if the user is not blocked."]}}');

        /** @var MockObject|\Exception $httpExceptionMock */
        $httpExceptionMock = $this->createMock(HttpExceptionInterface::class);
        $httpExceptionMock
            ->expects(self::once())
            ->method('getResponse')
            ->willReturn($responseMock);

        $subject = new RestClientException($httpExceptionMock);

        self::assertSame('Authentication failed. Please provide valid credentials and check if the user is not blocked.', $subject->getMessage());
        self::assertSame(0, $subject->getCode());
        self::assertSame($httpExceptionMock, $subject->getPrevious());
    }

    /**
     * @test
     */
    public function givenHttpExceptionInterfaceWithTwoErrors(): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock
            ->expects(self::once())
            ->method('getContent')
            ->with(false)
            ->willReturn('{"errors":{"-":["First error.", "Second error."]}}');

        /** @var MockObject|\Exception $httpExceptionMock */
        $httpExceptionMock = $this->createMock(HttpExceptionInterface::class);
        $httpExceptionMock
            ->expects(self::once())
            ->method('getResponse')
            ->willReturn($responseMock);

        $subject = new RestClientException($httpExceptionMock);

        self::assertSame('First error. / Second error.', $subject->getMessage());
        self::assertSame(0, $subject->getCode());
        self::assertSame($httpExceptionMock, $subject->getPrevious());
    }
}
