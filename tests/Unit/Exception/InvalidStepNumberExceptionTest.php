<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Tests\Unit\Exception;

use JobRouter\AddOn\RestClient\Exception\InvalidStepNumberException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InvalidStepNumberExceptionTest extends TestCase
{
    #[Test]
    public function forStepNumber(): void
    {
        $actual = InvalidStepNumberException::forStepNumber(0);

        self::assertSame('The given step number "0" is invalid, it must be an integer greater than 0', $actual->getMessage());
        self::assertSame(1671041151, $actual->getCode());
    }
}
