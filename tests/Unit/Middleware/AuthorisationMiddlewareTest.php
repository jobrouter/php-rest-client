<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Tests\Unit\Middleware;

use Brotkrueml\JobRouterClient\Middleware\AuthorisationMiddleware;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\TestCase;

class AuthorisationMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function handleRequestSetsNoHeaderWhenNoTokenIsAvailable(): void
    {
        $request = new Request('GET', '/');
        /** @var Request|null $newRequest */
        $newRequest = null;

        $middleware = new AuthorisationMiddleware();
        $middleware->handleRequest($request, function ($request) use (&$newRequest) {
            $newRequest = $request;
        });

        self::assertEmpty($newRequest->getHeaderLine('X-Jobrouter-Authorization'));
    }

    /**
     * @test
     */
    public function handleRequestSetsHeaderCorrectlyWhenTokenIsSet(): void
    {
        $request = new Request('GET', '/');
        /** @var Request|null $newRequest */
        $newRequest = null;

        $middleware = new AuthorisationMiddleware();
        $middleware->setToken('some-token');
        $middleware->handleRequest($request, function ($request) use (&$newRequest) {
            $newRequest = $request;
        });

        self::assertSame('Bearer some-token', $newRequest->getHeaderLine('X-Jobrouter-Authorization'));
    }

    /**
     * @test
     */
    public function handleRequestSetsNoHeaderWhenTokenIsSetAndAfterwardsReset(): void
    {
        $request = new Request('GET', '/');
        /** @var Request|null $newRequest */
        $newRequest = null;

        $middleware = new AuthorisationMiddleware();
        $middleware->setToken('some-token');
        $middleware->resetToken();
        $middleware->handleRequest($request, function ($request) use (&$newRequest) {
            $newRequest = $request;
        });

        self::assertEmpty($newRequest->getHeaderLine('X-Jobrouter-Authorization'));
    }
}
