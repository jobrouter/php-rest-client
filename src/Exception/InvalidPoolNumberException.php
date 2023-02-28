<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter Client.
 * https://github.com/brotkrueml/jobrouter-client
 *
 * Copyright (c) 2019-2023 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterClient\Exception;

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
