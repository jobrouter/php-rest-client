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

use JobRouter\AddOn\RestClient\Client\ClientDecorator;
use JobRouter\AddOn\RestClient\Client\ClientInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ClientDecoratorTest extends TestCase
{
    private ClientInterface&MockObject $client;
    private ClientDecorator $subject;

    protected function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->subject = $this->getMockForAbstractClass(
            ClientDecorator::class,
            [$this->client],
        );
    }

    #[Test]
    public function authenticateIsPassedToClient(): void
    {
        $this->client
            ->expects(self::once())
            ->method('authenticate');

        $this->subject->authenticate();
    }

    #[Test]
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

    #[Test]
    public function getJobRouterVersionIsPassedToClient(): void
    {
        $this->client
            ->expects(self::once())
            ->method('getJobRouterVersion');

        $this->subject->getJobRouterVersion();
    }
}
