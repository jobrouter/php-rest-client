<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Middleware;

use Buzz\Middleware\MiddlewareInterface;
use JobRouter\AddOn\RestClient\Information\Version;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
class UserAgentMiddleware implements MiddlewareInterface
{
    private const USER_AGENT_TEMPLATE = 'JobRouterClient/%s (https://github.com/jobrouter/php-rest-client) %s';

    private readonly string $userAgent;

    public function __construct(string $userAgentAddition = '')
    {
        $this->userAgent = \rtrim(
            \sprintf(
                self::USER_AGENT_TEMPLATE,
                (new Version())->getVersion(),
                $userAgentAddition,
            ),
        );
    }

    public function handleRequest(RequestInterface $request, callable $next): ?RequestInterface
    {
        $request = $request->withHeader('User-Agent', $this->userAgent);

        return $next($request);
    }

    public function handleResponse(RequestInterface $request, ResponseInterface $response, callable $next): ?ResponseInterface
    {
        return $next($request, $response);
    }
}
