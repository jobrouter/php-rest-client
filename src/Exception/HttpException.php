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

namespace Brotkrueml\JobRouterClient\Exception;

use Psr\Http\Client\ClientExceptionInterface;

final class HttpException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @internal
     */
    public static function fromRedirect(int $statusCode, string $resourceUrl, string $redirectUrl): self
    {
        $message = \sprintf(
            'Redirect "%d" from "%s" to "%s" occurred',
            $statusCode,
            $resourceUrl,
            $redirectUrl
        );

        return new static($message, $statusCode);
    }

    /**
     * @internal
     */
    public static function fromError(
        int $statusCode,
        string $resourceUrl,
        string $error,
        ClientExceptionInterface $e = null
    ): self {
        $message = \sprintf(
            'Error fetching resource "%s": %s',
            $resourceUrl,
            $error
        );

        return new static($message, $statusCode, $e);
    }
}
