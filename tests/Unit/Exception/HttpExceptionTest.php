<?php
declare(strict_types=1);

/**
 * This file is part of the JobRouter Client.
 *
 * Copyright (c) 2019-2021 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 *
 * @see https://github.com/brotkrueml/jobrouter-client
 */

namespace Brotkrueml\JobRouterClient\Tests\Unit\Exception;

use Brotkrueml\JobRouterClient\Exception\HttpException;
use Buzz\Exception\ClientException;
use PHPUnit\Framework\TestCase;

class HttpExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function fromRedirectReturnsInstantiatedHttpExceptionCorrectly(): void
    {
        $actual = HttpException::fromRedirect(307, 'http://example.org/', 'http://example.com/some/path/');

        self::assertInstanceOf(HttpException::class, $actual);
        self::assertSame(307, $actual->getCode());
        self::assertSame('Redirect "307" from "http://example.org/" to "http://example.com/some/path/" occurred', $actual->getMessage());
        self::assertNull($actual->getPrevious());
    }

    /**
     * @test
     */
    public function fromErrorReturnsInstantiatedHttpExceptionWithoutGivenPreviousExceptionCorrectly(): void
    {
        $actual = HttpException::fromError(501, 'http://example.org/', 'some error');

        self::assertInstanceOf(HttpException::class, $actual);
        self::assertSame(501, $actual->getCode());
        self::assertSame('Error fetching resource "http://example.org/": some error', $actual->getMessage());
        self::assertNull($actual->getPrevious());
    }

    /**
     * @test
     */
    public function fromErrorReturnsInstantiatedHttpExceptionWithGivenPreviousExceptionCorrectly(): void
    {
        $previous = new ClientException();

        $actual = HttpException::fromError(418, 'http://example.net/', 'another error', $previous);

        self::assertInstanceOf(HttpException::class, $actual);
        self::assertSame(418, $actual->getCode());
        self::assertSame('Error fetching resource "http://example.net/": another error', $actual->getMessage());
        self::assertSame($previous, $actual->getPrevious());
    }
}
