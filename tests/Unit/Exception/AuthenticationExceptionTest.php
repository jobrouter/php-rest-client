<?php
declare(strict_types=1);

namespace Unit\Exception;

use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
use Brotkrueml\JobRouterClient\Exception\AuthenticationException;
use PHPUnit\Framework\TestCase;

class AuthenticationExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function fromRestClientReturnsInstantiatedExceptionCorrectly(): void
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
            'Authentication failed for user "fake_user" on JobRouter base URL "http://example.org/',
            $actual->getMessage()
        );
        self::assertSame(12345, $actual->getCode());
        self::assertSame($previous, $actual->getPrevious());
    }
}
