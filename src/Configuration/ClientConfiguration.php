<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Configuration;

use JobRouter\AddOn\RestClient\Exception\InvalidConfigurationException;
use JobRouter\AddOn\RestClient\Exception\InvalidUrlException;
use JobRouter\AddOn\RestClient\Resource\JobRouterSystem;

/**
 * Value object that represents the configuration for a RestClient
 */
final class ClientConfiguration
{
    public const DEFAULT_TOKEN_LIFETIME_IN_SECONDS = 600;
    public const MINIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS = 0;
    public const MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS = 3600;

    private readonly JobRouterSystem $jobRouterSystem;
    private readonly string $username;
    private readonly string $password;
    private int $lifetime = self::DEFAULT_TOKEN_LIFETIME_IN_SECONDS;
    private string $userAgentAddition = '';
    private ClientOptions $clientOptions;

    /**
     * Creates a configuration instance for use in RestClient
     *
     * @param string $baseUrl The valid base URL of the JobRouter installation
     * @param string $username The username, must not be empty
     * @param string $password The password, must not be empty
     *
     * @throws InvalidConfigurationException A given parameter is not valid
     * @throws InvalidUrlException The base URL is not valid
     */
    public function __construct(
        string $baseUrl,
        string $username,
        #[\SensitiveParameter]
        string $password,
    ) {
        $this->mustNotHaveEmptyUsername($username);
        $this->mustNotHaveEmptyPassword($password);

        $this->jobRouterSystem = new JobRouterSystem($baseUrl);
        $this->username = $username;
        $this->password = $password;
        $this->clientOptions = new ClientOptions();
    }

    private function mustNotHaveEmptyUsername(string $username): void
    {
        if ($username === '') {
            throw new InvalidConfigurationException('Username must not be empty!', 1565710532);
        }
    }

    private function mustNotHaveEmptyPassword(string $password): void
    {
        if ($password === '') {
            throw new InvalidConfigurationException('Password must not be empty!', 1565710533);
        }
    }

    /**
     * @internal
     */
    public function getJobRouterSystem(): JobRouterSystem
    {
        return $this->jobRouterSystem;
    }

    /**
     * @internal
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @internal
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Return an instance with the specified lifetime
     *
     * @param int $lifetime A lifetime between 0 (ClientConfiguration::MINIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS)
     *                      and 3600 (ClientConfiguration::MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS).
     *                      The default value is 600 (ClientConfiguration::DEFAULT_TOKEN_LIFETIME_IN_SECONDS).
     *
     * @throws InvalidConfigurationException The given lifetime is not between 0 and 3600
     */
    public function withLifetime(int $lifetime): self
    {
        if ($lifetime === $this->lifetime) {
            return $this;
        }

        if (
            $lifetime < self::MINIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS
            || $lifetime > self::MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS
        ) {
            throw new InvalidConfigurationException(
                \sprintf(
                    'Lifetime value "%d" is not allowed! It has to be between "%d" and "%d"',
                    $lifetime,
                    self::MINIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS,
                    self::MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS,
                ),
                1565710534,
            );
        }

        $new = clone $this;
        $new->lifetime = $lifetime;

        return $new;
    }

    /**
     * Gets the lifetime
     */
    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    /**
     * Returns an instance with the specified user agent addition which will be appended to the original one
     *
     * @param string $userAgentAddition User agent addition
     */
    public function withUserAgentAddition(string $userAgentAddition): self
    {
        if ($userAgentAddition === $this->userAgentAddition) {
            return $this;
        }

        $new = clone $this;
        $new->userAgentAddition = $userAgentAddition;

        return $new;
    }

    /**
     * @internal
     */
    public function getUserAgentAddition(): string
    {
        return $this->userAgentAddition;
    }

    public function withClientOptions(ClientOptions $clientOptions): self
    {
        $new = clone $this;
        $new->clientOptions = $clientOptions;

        return $new;
    }

    /**
     * @internal
     */
    public function getClientOptions(): ClientOptions
    {
        return $this->clientOptions;
    }
}
