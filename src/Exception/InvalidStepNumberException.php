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

final class InvalidStepNumberException extends \DomainException implements ExceptionInterface
{
    public static function forStepNumber(int $stepNumber): self
    {
        return new self(
            \sprintf(
                'The given step number "%d" is invalid, it must be an integer greater than 0',
                $stepNumber,
            ),
            1671041151,
        );
    }
}
