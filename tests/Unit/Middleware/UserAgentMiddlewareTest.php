<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Tests\Unit\Middleware;

use JobRouter\AddOn\RestClient\Information\Version;
use JobRouter\AddOn\RestClient\Middleware\UserAgentMiddleware;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UserAgentMiddlewareTest extends TestCase
{
    #[Test]
    public function userAgentIsCorrectlyAddedToRequestHeader(): void
    {
        $request = new Request('GET', '/');
        /** @var Request|null $newRequest */
        $newRequest = null;

        $middleware = new UserAgentMiddleware();
        $middleware->handleRequest($request, static function ($request) use (&$newRequest): void {
            $newRequest = $request;
        });

        self::assertSame(
            \sprintf(
                'JobRouterClient/%s (https://jobrouter-client.rtfd.io/)',
                (new Version())->getVersion(),
            ),
            $newRequest->getHeaderLine('User-Agent'),
        );
    }

    #[Test]
    public function userAgentIsCorrectlyAddedToRequestHeaderWithAdditionGiven(): void
    {
        $request = new Request('GET', '/');
        /** @var Request|null $newRequest */
        $newRequest = null;

        $middleware = new UserAgentMiddleware('SomeConnector/1.2.3');
        $middleware->handleRequest($request, static function ($request) use (&$newRequest): void {
            $newRequest = $request;
        });

        self::assertSame(
            \sprintf(
                'JobRouterClient/%s (https://jobrouter-client.rtfd.io/) SomeConnector/1.2.3',
                (new Version())->getVersion(),
            ),
            $newRequest->getHeaderLine('User-Agent'),
        );
    }
}
