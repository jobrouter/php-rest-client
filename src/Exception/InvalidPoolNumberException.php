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

final class InvalidPoolNumberException extends \DomainException implements ExceptionInterface
{
    public static function forPoolNumber(int $poolNumber): self
    {
        return new self(
            \sprintf(
                'The given pool number "%d" is invalid, it must be an integer greater than 0',
                $poolNumber,
            ),
            1578228017,
        );
    }
}
