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
use Psr\Http\Message\RequestInterface;

/**
 * @internal
 */
class AuthorisationMiddleware
{
    public function __invoke(
        #[\SensitiveParameter]
        string &$token,
    ): callable {
        return static function (callable $handler) use (&$token): callable {
            return static function (RequestInterface $request, array $options) use ($handler, &$token): PromiseInterface {
                if ($token !== '') {
                    $request = $request->withHeader('X-Jobrouter-Authorization', 'Bearer ' . $token);
                }

                return $handler($request, $options);
            };
        };
    }
}
