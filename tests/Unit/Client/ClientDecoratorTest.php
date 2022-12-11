<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter Client.
 * https://github.com/brotkrueml/jobrouter-client
 *
 * Copyright (c) 2019-2022 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterClient\Tests\Unit\Client;

use Brotkrueml\JobRouterClient\Client\ClientDecorator;
use Brotkrueml\JobRouterClient\Client\ClientInterface;
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

    /**
     * @test
     */
    public function getJobRouterVersionIsPassedToClient(): void
    {
        $this->client
            ->expects(self::once())
            ->method('getJobRouterVersion');

        $this->subject->getJobRouterVersion();
    }
}
