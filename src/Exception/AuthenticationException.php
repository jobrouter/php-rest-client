<?php
declare(strict_types=1);

/**
 * This file is part of the JobRouter Client.
 *
 * Copyright (c) 2019-2021 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 *
 * @see https://github.com/brotkrueml/jobrouter-client
 */

namespace Brotkrueml\JobRouterClient\Exception;

use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;

final class AuthenticationException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @internal
     */
    public static function fromFailedAuthentication(
        ClientConfiguration $configuration,
        int $code = 0,
        \Exception $previous = null
    ): self {
        $message = \sprintf(
            'Authentication failed for user "%s" on JobRouter base URL "%s"',
            $configuration->getUsername(),
            (string)$configuration->getJobRouterSystem()
        );

        return new self($message, $code, $previous);
    }
}
