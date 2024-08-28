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

use JobRouter\AddOn\RestClient\Configuration\ClientConfiguration;
use JobRouter\AddOn\RestClient\Exception\AuthenticationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(AuthenticationException::class)]
final class AuthenticationExceptionTest extends TestCase
{
    #[Test]
    public function fromFailedAuthenticationReturnsInstantiatedExceptionWithAllArgumentsCorrectly(): void
    {
        $configuration = new ClientConfiguration(
            'http://example.org/',
            'fake_user',
            'fake_pass',
        );

        $previous = new \RuntimeException();

        $actual = AuthenticationException::fromFailedAuthentication($configuration, 12345, $previous);

        self::assertInstanceOf(AuthenticationException::class, $actual);
        self::assertSame(
            'Authentication failed for user "fake_user" on JobRouter base URL "http://example.org/"',
            $actual->getMessage(),
        );
        self::assertSame(12345, $actual->getCode());
        self::assertSame($previous, $actual->getPrevious());
    }

    #[Test]
    public function fromFailedAuthenticationReturnsInstantiatedExceptionWithOnlyRequiredArgumentsCorrectly(): void
    {
        $configuration = new ClientConfiguration(
            'http://example.org/',
            'fake_user',
            'fake_pass',
        );

        $actual = AuthenticationException::fromFailedAuthentication($configuration);

        self::assertInstanceOf(AuthenticationException::class, $actual);
        self::assertSame(
            'Authentication failed for user "fake_user" on JobRouter base URL "http://example.org/"',
            $actual->getMessage(),
        );
        self::assertSame(0, $actual->getCode());
        self::assertNull($actual->getPrevious());
    }

    #[Test]
    public function fromActivatedNtlm(): void
    {
        $actual = AuthenticationException::fromActivatedNtlm();

        self::assertInstanceOf(AuthenticationException::class, $actual);
        self::assertSame(
            'The authenticate() method must not be used, as NTLM is activated',
            $actual->getMessage(),
        );
        self::assertSame(1724833066, $actual->getCode());
    }
}
