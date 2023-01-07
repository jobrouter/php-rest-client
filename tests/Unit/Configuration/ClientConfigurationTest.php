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

namespace Brotkrueml\JobRouterClient\Tests\Unit\Configuration;

use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
use Brotkrueml\JobRouterClient\Configuration\ClientOptions;
use Brotkrueml\JobRouterClient\Exception\InvalidConfigurationException;
use PHPUnit\Framework\TestCase;

class ClientConfigurationTest extends TestCase
{
    private ClientConfiguration $subject;

    protected function setUp(): void
    {
        $this->subject = new ClientConfiguration(
            'http://example.org/jobrouter/',
            'fake_username',
            'fake_password',
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
            'fake_password',
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
            '',
        );
    }

    /**
     * @test
     */
    public function getBaseUrlReturnsPreviouslySetBaseUrl(): void
    {
        $actual = $this->subject->getJobRouterSystem();
        $expected = 'http://example.org/jobrouter/';

        self::assertSame($expected, (string)$actual);
    }

    /**
     * @test
     */
    public function getUsernameReturnsPreviouslySetUsername(): void
    {
        $actual = $this->subject->getUsername();

        self::assertSame('fake_username', $actual);
    }

    /**
     * @test
     */
    public function getPasswordReturnsPreviouslySetPassword(): void
    {
        $actual = $this->subject->getPassword();

        self::assertSame('fake_password', $actual);
    }

    /**
     * @test
     */
    public function getLifetimeReturnsTheDefaultLifetime(): void
    {
        $actual = $this->subject->getLifetime();

        self::assertSame(ClientConfiguration::DEFAULT_TOKEN_LIFETIME_IN_SECONDS, $actual);
    }

    /**
     * @test
     */
    public function withLifetimeGivesSameObjectBackWhenDefinedLifetimeIsIdentical(): void
    {
        $subject = $this->subject->withLifetime(42);
        self::assertInstanceOf(ClientConfiguration::class, $subject);
        self::assertNotSame($subject, $this->subject);

        $newSubject = $subject->withLifetime(42);
        self::assertSame($subject, $newSubject);
    }

    /**
     * @test
     */
    public function withLifetimeThrowsExceptionOnUnderrunMinimumAllowedLifetime(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1565710534);

        $this->subject->withLifetime(-1);
    }

    /**
     * @test
     */
    public function withLifetimeThrowsExceptionOnOverrunMaximumAllowedLifetime(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1565710534);

        $this->subject->withLifetime(ClientConfiguration::MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS + 1);
    }

    /**
     * @test
     */
    public function withMinimumAllowedLifetimeIsOkay(): void
    {
        $configuration = $this->subject->withLifetime(ClientConfiguration::MINIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS);

        $actual = $configuration->getLifetime();

        self::assertSame(ClientConfiguration::MINIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS, $actual);
    }

    /**
     * @test
     */
    public function withLifetimeInAllowedBoundsIsOkay(): void
    {
        $configuration = $this->subject->withLifetime(42);

        $actual = $configuration->getLifetime();

        self::assertSame(42, $actual);
    }

    /**
     * @test
     */
    public function withMaximumAllowedLifetimeIsOkay(): void
    {
        $configuration = $this->subject->withLifetime(ClientConfiguration::MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS);

        $actual = $configuration->getLifetime();

        self::assertSame(ClientConfiguration::MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS, $actual);
    }

    /**
     * @test
     */
    public function withUserAgentAdditionReturnsCorrectInstances(): void
    {
        $subject = $this->subject->withUserAgentAddition('AdditionToUserAgent');
        self::assertInstanceOf(ClientConfiguration::class, $subject);
        self::assertNotSame($subject, $this->subject);

        $newSubject = $subject->withUserAgentAddition('AdditionToUserAgent');
        self::assertSame($subject, $newSubject);
    }

    /**
     * @test
     */
    public function getUserAgentAdditionReturnsEmptyStringWhenNotConfigured(): void
    {
        $actual = $this->subject->getUserAgentAddition();

        self::assertSame('', $actual);
    }

    /**
     * @test
     */
    public function withUserAgentAdditionSetsAdditionalUserAgentCorrectly(): void
    {
        $subject = $this->subject->withUserAgentAddition('SomeUserAgentAddition');

        $actual = $subject->getUserAgentAddition();

        self::assertSame('SomeUserAgentAddition', $actual);
    }

    /**
     * @test
     */
    public function withClientOptionsReturnsNewInstanceOfSubject(): void
    {
        $subject = $this->subject->withClientOptions(new ClientOptions());
        self::assertInstanceOf(ClientConfiguration::class, $subject);
        self::assertNotSame($subject, $this->subject);
    }

    /**
     * @test
     */
    public function getClientOptionsReturnsDefaultOptionsWhenNotConfigured(): void
    {
        $expectedOptions = (new ClientOptions())->toArray();

        self::assertSame($expectedOptions, $this->subject->getClientOptions()->toArray());
    }

    /**
     * @test
     */
    public function withClientOptionsSetClientOptionsCorrectly(): void
    {
        $newClientOptions = new ClientOptions();

        $newSubject = $this->subject->withClientOptions($newClientOptions);

        self::assertSame($newClientOptions, $newSubject->getClientOptions());
    }
}
