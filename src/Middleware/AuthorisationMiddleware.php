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
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
class AuthorisationMiddleware implements MiddlewareInterface
{
    private ?string $token = null;

    public function setToken(
        #[\SensitiveParameter]
        string $token,
    ): void {
        $this->token = $token;
    }

    public function resetToken(): void
    {
        $this->token = null;
    }

    public function handleRequest(RequestInterface $request, callable $next): ?RequestInterface
    {
        if ($this->token) {
            $request = $request->withHeader('X-Jobrouter-Authorization', 'Bearer ' . $this->token);
        }

        return $next($request);
    }

    public function handleResponse(RequestInterface $request, ResponseInterface $response, callable $next): ?ResponseInterface
    {
        return $next($request, $response);
    }
}
