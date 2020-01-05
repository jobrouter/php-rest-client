<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Middleware;

use Brotkrueml\JobRouterClient\Version;
use Buzz\Middleware\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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
        $this->userAgent = \rtrim(
            \sprintf(
                static::USER_AGENT_TEMPLATE,
                Version::VERSION,
                $userAgentAddition
            )
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