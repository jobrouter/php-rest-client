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

use JobRouter\AddOn\RestClient\Middleware\AuthorisationMiddleware;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AuthorisationMiddlewareTest extends TestCase
{
    #[Test]
    public function handleRequestSetsNoHeaderWhenNoTokenIsAvailable(): void
    {
        $request = new Request('GET', '/');
        /** @var Request|null $newRequest */
        $newRequest = null;

        $middleware = new AuthorisationMiddleware();
        $middleware->handleRequest($request, static function ($request) use (&$newRequest): void {
            $newRequest = $request;
        });

        self::assertEmpty($newRequest->getHeaderLine('X-Jobrouter-Authorization'));
    }

    #[Test]
    public function handleRequestSetsHeaderCorrectlyWhenTokenIsSet(): void
    {
        $request = new Request('GET', '/');
        /** @var Request|null $newRequest */
        $newRequest = null;

        $middleware = new AuthorisationMiddleware();
        $middleware->setToken('some-token');
        $middleware->handleRequest($request, static function ($request) use (&$newRequest): void {
            $newRequest = $request;
        });

        self::assertSame('Bearer some-token', $newRequest->getHeaderLine('X-Jobrouter-Authorization'));
    }

    #[Test]
    public function handleRequestSetsNoHeaderWhenTokenIsSetAndAfterwardsReset(): void
    {
        $request = new Request('GET', '/');
        /** @var Request|null $newRequest */
        $newRequest = null;

        $middleware = new AuthorisationMiddleware();
        $middleware->setToken('some-token');
        $middleware->resetToken();
        $middleware->handleRequest($request, static function ($request) use (&$newRequest): void {
            $newRequest = $request;
        });

        self::assertEmpty($newRequest->getHeaderLine('X-Jobrouter-Authorization'));
    }
}
