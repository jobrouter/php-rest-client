<?php
declare(strict_types = 1);

namespace Brotkrueml\JobRouterClient\Configuration;

final class ClientConfiguration
{
    public const API_ENDPOINT = 'api/rest/v2/';
    public const DEFAULT_TOKEN_LIFETIME_IN_SECONDS = 600;
    public const MINIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS = 0;
    public const MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS = 3600;

    private $restBaseUri;
    private $username;
    private $password;
    private $lifetime = self::DEFAULT_TOKEN_LIFETIME_IN_SECONDS;

    public function __construct(string $baseUri, string $username, string $password)
    {
        $baseUri = \filter_var($baseUri, FILTER_VALIDATE_URL);

        if ($baseUri === false) {
            throw new \InvalidArgumentException(
                sprintf('Given baseUri "%s" is not a valid URL!', $baseUri),
                1565710531
            );
        }

        if (empty($username)) {
            throw new \InvalidArgumentException('Empty username is not allowed!', 1565710532);
        }

        if (empty($password)) {
            throw new \InvalidArgumentException('Empty password is not allowed!', 1565710533);
        }

        $this->restBaseUri = \rtrim($baseUri, '/') . '/' . self::API_ENDPOINT;
        $this->username = $username;
        $this->password = $password;
    }

    public function getRestApiUri(): string
    {
        return $this->restBaseUri;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setLifetime(int $lifetime): void
    {
        if (
            $lifetime < self::MINIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS
            || $lifetime > self::MAXIMUM_ALLOWED_TOKEN_LIFETIME_IN_SECONDS
        ) {
            throw new \InvalidArgumentException(
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

    public function getLifetime(): int
    {
        return $this->lifetime;
    }
}
