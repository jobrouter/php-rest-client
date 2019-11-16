<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Configuration;

use Brotkrueml\JobRouterClient\Exception\InvalidConfigurationException;

/**
 * Value object that represents the configuration for a RestClient
 */
final class ClientConfiguration
{
    public const DEFAULT_TOKEN_LIFETIME_IN_SECONDS = 600;
    public const MINIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS = 0;
    public const MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS = 3600;

    /**
     * @var string
     * @readonly
     */
    private $baseUrl;

    /**
     * @var string
     * @readonly
     */
    private $username;

    /**
     * @var string
     * @readonly
     */
    private $password;

    /**
     * @var int
     */
    private $lifetime = self::DEFAULT_TOKEN_LIFETIME_IN_SECONDS;

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
                sprintf('Given baseUrl "%s" is not a valid URL!', $baseUrl),
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
     * Sets the lifetime of a session in seconds
     * Must be between 0 (ClientConfiguration::MINIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS)
     * and 3600 (ClientConfiguration::MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS).
     * Default value is 600 (ClientConfiguration::DEFAULT_TOKEN_LIFETIME_IN_SECONDS)
     *
     * @param int $lifetime
     *
     * @throws InvalidConfigurationException The given lifetime is not between 0 and 3600
     */
    public function setLifetime(int $lifetime): void
    {
        if (
            $lifetime < self::MINIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS
            || $lifetime > self::MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS
        ) {
            throw new InvalidConfigurationException(
                sprintf(
                    'Lifetime value "%d" is not allowed! It has to be between "%d" and "%d"',
                    $lifetime,
                    self::MINIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS,
                    self::MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS
                ),
                1565710534
            );
        }

        $this->lifetime = $lifetime;
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
}
