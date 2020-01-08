<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Exception;

use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;

final class AuthenticationException extends \RuntimeException implements ExceptionInterface
{
    public static function fromFailedAuthentication(
        ClientConfiguration $configuration,
        int $code = 0,
        \Exception $previous = null
    ): self {
        $message = \sprintf(
            'Authentication failed for user "%s" on JobRouter base URL "%s',
            $configuration->getUsername(),
            $configuration->getBaseUrl()
        );

        return new static($message, $code, $previous);
    }
}
