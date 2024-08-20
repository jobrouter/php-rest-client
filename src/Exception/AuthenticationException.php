<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Exception;

use JobRouter\AddOn\RestClient\Configuration\ClientConfiguration;

final class AuthenticationException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @internal
     */
    public static function fromFailedAuthentication(
        ClientConfiguration $configuration,
        int $code = 0,
        ?\Exception $previous = null,
    ): self {
        $message = \sprintf(
            'Authentication failed for user "%s" on JobRouter base URL "%s"',
            $configuration->getUsername(),
            $configuration->getJobRouterSystem(),
        );

        return new self($message, $code, $previous);
    }
}
