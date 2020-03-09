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

namespace Brotkrueml\JobRouterClient\Configuration;

use Brotkrueml\JobRouterClient\Exception\InvalidConfigurationException;

/**
 * Value object that represents the configuration for a RestClient
 * @psalm-immutable
 */
final class ClientConfiguration
{
    public const DEFAULT_TOKEN_LIFETIME_IN_SECONDS = 600;
    public const MINIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS = 0;
    public const MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS = 3600;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $lifetime = self::DEFAULT_TOKEN_LIFETIME_IN_SECONDS;

    /**
     * @var string
     */
    private $userAgentAddition = '';

    /**
     * Creates a configuration instance for use in RestClient
     *
     * @param string $baseUrl The valid base URL of the JobRouter installation
     * @param string $username The username, must not be empty
     * @param string $password The password, must not be empty
     *
     * @throws InvalidConfigurationException A given parameter is not valid
     */
    public function __construct(string $baseUrl, string $username, string $password)
    {
        $filteredBaseUrl = \filter_var($baseUrl, FILTER_VALIDATE_URL);

        if ($filteredBaseUrl === false) {
            throw new InvalidConfigurationException(
                \sprintf('Given baseUrl "%s" is not a valid URL!', $baseUrl),
                1565710531
            );
        }

        if (empty($username)) {
            throw new InvalidConfigurationException('Username must not be empty!', 1565710532);
        }

        if (empty($password)) {
            throw new InvalidConfigurationException('Password must not be empty!', 1565710533);
        }

        $this->baseUrl = $baseUrl;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Gets the base url of the JobRouter installation
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Gets the username
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Gets the password
     *
     * @return string
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
     * @return self
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
                    self::MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS
                ),
                1565710534
            );
        }

        $new = clone $this;
        $new->lifetime = $lifetime;

        return $new;
    }

    /**
     * Gets the lifetime
     *
     * @return int
     */
    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    /**
     * Returns an instance with the specified user agent addition which will be appended to the original one
     *
     * @param string $userAgentAddition User agent addition
     * @return self
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
     * Gets the user agent addition
     *
     * @return string
     */
    public function getUserAgentAddition(): string
    {
        return $this->userAgentAddition;
    }
}
