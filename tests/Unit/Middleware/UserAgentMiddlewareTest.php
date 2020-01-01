<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Tests\Unit\Middleware;

use Brotkrueml\JobRouterClient\Environment\Version;
use Brotkrueml\JobRouterClient\Middleware\UserAgentMiddleware;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\TestCase;

class UserAgentMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function userAgentIsCorrectlyAddedToRequestHeader()
    {
        $request = new Request('GET', '/');
        /** @var Request|null $newRequest */
        $newRequest = null;

        $middleware = new UserAgentMiddleware();
        $middleware->handleRequest($request, function ($request) use (&$newRequest) {
            $newRequest = $request;
        });

        self::assertSame(
            \sprintf(
                'JobRouterClient/%s (https://github.com/brotkrueml/jobrouter-client)',
                Version::VERSION
            ),
            $newRequest->getHeaderLine('User-Agent')
        );
    }

    /**
     * @test
     */
    public function userAgentIsCorrectlyAddedToRequestHeaderWithAdditionGiven()
    {
        $request = new Request('GET', '/');
        /** @var Request|null $newRequest */
        $newRequest = null;

        $middleware = new UserAgentMiddleware('SomeConnector/1.2.3');
        $middleware->handleRequest($request, function ($request) use (&$newRequest) {
            $newRequest = $request;
        });

        self::assertSame(
            \sprintf(
                'JobRouterClient/%s (https://github.com/brotkrueml/jobrouter-client) SomeConnector/1.2.3',
                Version::VERSION
            ),
            $newRequest->getHeaderLine('User-Agent')
        );
    }
}
