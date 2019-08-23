<?php
declare(strict_types = 1);

namespace Unit\Exception;

use Brotkrueml\JobRouterClient\Exception\RestException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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
