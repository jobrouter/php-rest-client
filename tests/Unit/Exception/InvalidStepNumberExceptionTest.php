<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter Client.
 * https://github.com/brotkrueml/jobrouter-client
 *
 * Copyright (c) 2019-2022 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterClient\Tests\Unit\Exception;

use Brotkrueml\JobRouterClient\Exception\InvalidStepNumberException;
use PHPUnit\Framework\TestCase;

final class InvalidStepNumberExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function forStepNumber(): void
    {
        $actual = InvalidStepNumberException::forStepNumber(0);

        self::assertSame('The given step number "0" is invalid, it must be an integer greater than 0', $actual->getMessage());
        self::assertSame(1671041151, $actual->getCode());
    }
}
