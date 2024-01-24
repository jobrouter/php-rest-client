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

use JobRouter\AddOn\RestClient\Exception\InvalidPoolNumberException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InvalidPoolNumberExceptionTest extends TestCase
{
    #[Test]
    public function forPoolNumber(): void
    {
        $actual = InvalidPoolNumberException::forPoolNumber(0);

        self::assertSame('The given pool number "0" is invalid, it must be an integer greater than 0', $actual->getMessage());
        self::assertSame(1578228017, $actual->getCode());
    }
}
