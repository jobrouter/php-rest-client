<?php
declare(strict_types=1);

/**
 * This file is part of the JobRouter Client.
 *
 * Copyright (c) 2019-2021 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 *
 * @see https://github.com/brotkrueml/jobrouter-client
 */

namespace Brotkrueml\JobRouterClient\Tests\Unit\Middleware;

use Brotkrueml\JobRouterClient\Information\Version;
use Brotkrueml\JobRouterClient\Middleware\UserAgentMiddleware;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\TestCase;

class UserAgentMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function userAgentIsCorrectlyAddedToRequestHeader(): void
    {
        $request = new Request('GET', '/');
        /** @var Request|null $newRequest */
        $newRequest = null;

        $middleware = new UserAgentMiddleware();
        $middleware->handleRequest($request, function ($request) use (&$newRequest): void {
            $newRequest = $request;
        });

        self::assertSame(
            \sprintf(
                'JobRouterClient/%s (https://jobrouter-client.rtfd.io/)',
                (new Version())->getVersion()
            ),
            $newRequest->getHeaderLine('User-Agent')
        );
    }

    /**
     * @test
     */
    public function userAgentIsCorrectlyAddedToRequestHeaderWithAdditionGiven(): void
    {
        $request = new Request('GET', '/');
        /** @var Request|null $newRequest */
        $newRequest = null;

        $middleware = new UserAgentMiddleware('SomeConnector/1.2.3');
        $middleware->handleRequest($request, function ($request) use (&$newRequest): void {
            $newRequest = $request;
        });

        self::assertSame(
            \sprintf(
                'JobRouterClient/%s (https://jobrouter-client.rtfd.io/) SomeConnector/1.2.3',
                (new Version())->getVersion()
            ),
            $newRequest->getHeaderLine('User-Agent')
        );
    }
}
