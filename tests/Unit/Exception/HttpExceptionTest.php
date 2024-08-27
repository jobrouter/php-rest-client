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

use JobRouter\AddOn\RestClient\Exception\HttpException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;

class HttpExceptionTest extends TestCase
{
    #[Test]
    public function fromRedirectReturnsInstantiatedHttpExceptionCorrectly(): void
    {
        $actual = HttpException::fromRedirect(307, 'http://example.org/', 'http://example.com/some/path/');

        self::assertInstanceOf(HttpException::class, $actual);
        self::assertSame(307, $actual->getCode());
        self::assertSame(
            'Redirect "307" from "http://example.org/" to "http://example.com/some/path/" occurred',
            $actual->getMessage(),
        );
        self::assertNull($actual->getPrevious());
    }

    #[Test]
    public function fromErrorReturnsInstantiatedHttpExceptionWithoutGivenPreviousExceptionCorrectly(): void
    {
        $actual = HttpException::fromError(501, 'http://example.org/', 'some error');

        self::assertInstanceOf(HttpException::class, $actual);
        self::assertSame(501, $actual->getCode());
        self::assertSame('Error fetching resource "http://example.org/": some error', $actual->getMessage());
        self::assertNull($actual->getPrevious());
    }

    #[Test]
    public function fromErrorReturnsInstantiatedHttpExceptionWithGivenPreviousExceptionCorrectly(): void
    {
        $previous = new class extends \RuntimeException implements ClientExceptionInterface {};

        $actual = HttpException::fromError(418, 'http://example.net/', 'another error', $previous);

        self::assertInstanceOf(HttpException::class, $actual);
        self::assertSame(418, $actual->getCode());
        self::assertSame('Error fetching resource "http://example.net/": another error', $actual->getMessage());
        self::assertSame($previous, $actual->getPrevious());
    }
}
