<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Tests\Unit\Configuration;

use JobRouter\AddOn\RestClient\Configuration\ClientOptions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ClientOptionsTest extends TestCase
{
    #[Test]
    public function toArrayReturnsArrayCorrectlyWithDefaultValues(): void
    {
        $subject = new ClientOptions();

        $actual = $subject->toArray();

        self::assertCount(5, $actual);
        self::assertFalse($actual['allow_redirects']);
        self::assertSame(5, $actual['max_redirects']);
        self::assertSame(0, $actual['timeout']);
        self::assertTrue($actual['verify']);
        self::assertNull($actual['proxy']);
    }

    #[Test]
    public function toArrayReturnsArrayCorrectlyWithGivenValues(): void
    {
        $subject = new ClientOptions(true, 10, 42, false, 'http://example.org/');

        $actual = $subject->toArray();

        self::assertCount(5, $actual);
        self::assertTrue($actual['allow_redirects']);
        self::assertSame(10, $actual['max_redirects']);
        self::assertSame(42, $actual['timeout']);
        self::assertFalse($actual['verify']);
        self::assertSame('http://example.org/', $actual['proxy']);
    }
}
