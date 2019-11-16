<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Tests\Unit\Configuration;

use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
use Brotkrueml\JobRouterClient\Exception\InvalidConfigurationException;
use PHPUnit\Framework\TestCase;

class ClientConfigurationTest extends TestCase
{
    /** @var ClientConfiguration */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new ClientConfiguration(
            'http://example.org/jobrouter/',
            'fake_username',
            'fake_password'
        );
    }

    /**
     * @test
     */
    public function constructThrowsExceptionOnInvalidBaseUri(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1565710531);

        new ClientConfiguration(
            'invalidBaseUri',
            'fake_username',
            'fake_password'
        );
    }

    /**
     * @test
     */
    public function constructThrowsExceptionOnEmptyUsername(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1565710532);

        new ClientConfiguration(
            'https://example.org/',
            '',
            'fake_password'
        );
    }

    /**
     * @test
     */
    public function constructThrowsExceptionOnEmptyPassword(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1565710533);

        new ClientConfiguration(
            'https://example.org/',
            'fake_username',
            ''
        );
    }

    /**
     * @test
     */
    public function getBaseUrlReturnsPreviouslySetBaseUrl(): void
    {
        $actual = $this->subject->getBaseUrl();
        $expected = 'http://example.org/jobrouter/';

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getUsernameReturnsPreviouslySetUsername(): void
    {
        $actual = $this->subject->getUsername();

        $this->assertSame('fake_username', $actual);
    }

    /**
     * @test
     */
    public function getPasswordReturnsPreviouslySetPassword(): void
    {
        $actual = $this->subject->getPassword();

        $this->assertSame('fake_password', $actual);
    }

    /**
     * @test
     */
    public function getLifetimeReturnsTheDefaultLifetime(): void
    {
        $actual = $this->subject->getLifetime();

        $this->assertSame(ClientConfiguration::DEFAULT_TOKEN_LIFETIME_IN_SECONDS, $actual);
    }

    /**
     * @test
     */
    public function setLifetimeThrowsExceptionOnUnderrunMinimumAllowedLifetime(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1565710534);

        $this->subject->setLifetime(ClientConfiguration::MINIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS - 1);
    }

    /**
     * @test
     */
    public function setLifetimeThrowsExceptionOnOverrunMaximumAllowedLifetime(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1565710534);

        $this->subject->setLifetime(ClientConfiguration::MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS + 1);
    }

    /**
     * @test
     */
    public function setMinimumAllowedLifetimeIsOkay(): void
    {
        $this->subject->setLifetime(ClientConfiguration::MINIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS);

        $actual = $this->subject->getLifetime();

        $this->assertSame(ClientConfiguration::MINIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS, $actual);
    }

    /**
     * @test
     */
    public function setLifetimeInAllowedBoundsIsOkay(): void
    {
        $this->subject->setLifetime(42);

        $actual = $this->subject->getLifetime();

        $this->assertSame(42, $actual);
    }

    /**
     * @test
     */
    public function setMaximumAllowedLifetimeIsOkay(): void
    {
        $this->subject->setLifetime(ClientConfiguration::MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS);

        $actual = $this->subject->getLifetime();

        $this->assertSame(ClientConfiguration::MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS, $actual);
    }
}
