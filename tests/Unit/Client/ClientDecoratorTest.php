<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Tests\Unit\Client;

use Brotkrueml\JobRouterClient\Client\ClientDecorator;
use Brotkrueml\JobRouterClient\Client\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

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

    protected function setUp(): void
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
        $responseStub = $this->createStub(ResponseInterface::class);

        $this->client
            ->expects(self::once())
            ->method('request')
            ->with('some method', 'some resource', 'some data')
            ->willReturn($responseStub);

        $actual = $this->subject->request('some method', 'some resource', 'some data');

        self::assertSame($responseStub, $actual);
    }
}
