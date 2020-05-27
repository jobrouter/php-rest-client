<?php
declare(strict_types=1);

/**
 * This file is part of the JobRouter Client.
 *
 * Copyright (c) 2019-2020 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 *
 * @see https://github.com/brotkrueml/jobrouter-client
 */

namespace Brotkrueml\JobRouterClient\Middleware;

use Brotkrueml\JobRouterClient\Information\Version;
use Buzz\Middleware\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
class UserAgentMiddleware implements MiddlewareInterface
{
    private const USER_AGENT_TEMPLATE = 'JobRouterClient/%s (https://github.com/brotkrueml/jobrouter-client) %s';

    /**
     * @var string
     * @readonly
     */
    private $userAgent;

    public function __construct(string $userAgentAddition = '')
    {
        /** @psalm-suppress MixedArgument */
        $this->userAgent = \rtrim(
            \sprintf(
                static::USER_AGENT_TEMPLATE,
                (new Version())->getVersion(),
                $userAgentAddition
            )
        );
    }

    /**
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function handleRequest(RequestInterface $request, callable $next): ?RequestInterface
    {
        $request = $request->withHeader('User-Agent', $this->userAgent);

        return $next($request);
    }

    /**
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function handleResponse(RequestInterface $request, ResponseInterface $response, callable $next): ?ResponseInterface
    {
        return $next($request, $response);
    }
}
