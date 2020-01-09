<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Middleware;

use Buzz\Middleware\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
class AuthorisationMiddleware implements MiddlewareInterface
{
    /**
     * @var string|null
     */
    private $token;

    public function setToken(string $token): void
    {
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
