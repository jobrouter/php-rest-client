<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Tests\Unit\Client;

use Brotkrueml\JobRouterClient\Client\ClientDecorator;
use Brotkrueml\JobRouterClient\Client\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ClientDecoratorTest extends TestCase
{
    /**
     * @var ClientInterface|MockObject
     */
    private $client;

    /**
     * @var ClientDecorator
     */
    private $subject;

    public function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->subject = $this->getMockForAbstractClass(
            ClientDecorator::class,
            [$this->client]
        );
    }

    /**
     * @test
     */
    public function authenticateIsPassedToClient(): void
    {
        $this->client
            ->expects(self::once())
            ->method('authenticate');

        $this->subject->authenticate();
    }

    /**
     * @test
     */
    public function requestIsPassedToClient(): void
    {
        $response = $this->getImplementedResponse();

        $this->client
            ->expects(self::once())
            ->method('request')
            ->with('some method', 'some resource', 'some data')
            ->willReturn($response);

        $actual = $this->subject->request('some method', 'some resource', 'some data');

        self::assertSame($response, $actual);
    }

    private function getImplementedResponse()
    {
        return new class implements ResponseInterface {
            public function getProtocolVersion()
            {
            }

            public function withProtocolVersion($version)
            {
            }

            public function getHeaders()
            {
            }

            public function hasHeader($name)
            {
            }

            public function getHeader($name)
            {
            }

            public function getHeaderLine($name)
            {
            }

            public function withHeader($name, $value)
            {
            }

            public function withAddedHeader($name, $value)
            {
            }

            public function withoutHeader($name)
            {
            }

            public function getBody()
            {
            }

            public function withBody(StreamInterface $body)
            {
            }

            public function getStatusCode()
            {
            }

            public function withStatus($code, $reasonPhrase = '')
            {
            }

            public function getReasonPhrase()
            {
            }
        };
    }
}
