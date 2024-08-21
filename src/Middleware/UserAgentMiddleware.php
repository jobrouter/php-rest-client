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

use GuzzleHttp\Promise\PromiseInterface;
use JobRouter\AddOn\RestClient\Information\Version;
use Psr\Http\Message\RequestInterface;

/**
 * @internal
 */
class UserAgentMiddleware
{
    private const USER_AGENT_TEMPLATE = 'JobRouterClient/%s (https://github.com/jobrouter/php-rest-client) %s';

    public function __invoke(string $userAgentAddition): callable
    {
        $userAgent = $this->compileUserAgent($userAgentAddition);

        return static fn(callable $handler): callable =>
            static function (RequestInterface $request, array $options) use ($handler, $userAgent): PromiseInterface {
                $request = $request->withHeader('User-Agent', $userAgent);

                return $handler($request, $options);
            };
    }

    private function compileUserAgent(string $userAgentAddition): string
    {
        return \rtrim(
            \sprintf(
                self::USER_AGENT_TEMPLATE,
                (new Version())->getVersion(),
                $userAgentAddition,
            ),
        );
    }
}
