<?php
declare(strict_types=1);

/**
 * This file is part of the JobRouter Client.
 *
 * Copyright (c) 2019-2020 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 *
 * @see https://github.com/brotkrueml/jobrouter-client
 */

namespace Brotkrueml\JobRouterClient\Tests\Unit\Exception;

use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
use Brotkrueml\JobRouterClient\Exception\AuthenticationException;
use PHPUnit\Framework\TestCase;

class AuthenticationExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function fromFailedAuthenticationReturnsInstantiatedExceptionWithAllArgumentsCorrectly(): void
    {
        $configuration = new ClientConfiguration(
            'http://example.org/',
            'fake_user',
            'fake_pass'
        );

        $previous = new \RuntimeException();

        $actual = AuthenticationException::fromFailedAuthentication($configuration, 12345, $previous);

        self::assertInstanceOf(AuthenticationException::class, $actual);
        self::assertSame(
            'Authentication failed for user "fake_user" on JobRouter base URL "http://example.org/"',
            $actual->getMessage()
        );
        self::assertSame(12345, $actual->getCode());
        self::assertSame($previous, $actual->getPrevious());
    }

    /**
     * @test
     */
    public function fromFailedAuthenticationReturnsInstantiatedExceptionWithOnlyRequiredArgumentsCorrectly(): void
    {
        $configuration = new ClientConfiguration(
            'http://example.org/',
            'fake_user',
            'fake_pass'
        );

        $actual = AuthenticationException::fromFailedAuthentication($configuration);

        self::assertInstanceOf(AuthenticationException::class, $actual);
        self::assertSame(
            'Authentication failed for user "fake_user" on JobRouter base URL "http://example.org/"',
            $actual->getMessage()
        );
        self::assertSame(0, $actual->getCode());
        self::assertNull($actual->getPrevious());
    }
}
