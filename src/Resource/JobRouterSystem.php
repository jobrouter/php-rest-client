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

namespace Brotkrueml\JobRouterClient\Resource;

use Brotkrueml\JobRouterClient\Exception\InvalidUrlException;

/**
 * Value object that represents the a JobRouter system
 * @internal
 * @psalm-immutable
 */
final class JobRouterSystem implements \Stringable
{
    private const API_ENDPOINT = 'api/rest/v2/';

    /**
     * @var string
     */
    private $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->mustHaveValidBaseUrl($baseUrl);

        $this->baseUrl = \rtrim($baseUrl, '/') . '/';
    }

    private function mustHaveValidBaseUrl(string $url): void
    {
        if (!$this->isValidUrl($url)) {
            throw new InvalidUrlException(
                \sprintf('Given baseUrl "%s" is not valid. It must consist of a scheme, the host and the path!', $url),
                1565710531
            );
        }

        if (!$this->urlHasHttpProtocolScheme($url)) {
            throw new InvalidUrlException(
                \sprintf('Given baseUrl "%s" must have a HTTP protocol scheme!', $url),
                1586538201
            );
        }

        if ($this->urlHasQueryParameters($url)) {
            throw new InvalidUrlException(
                \sprintf('Given baseUrl "%s" must not have query parameters!', $url),
                1586538700
            );
        }

        if ($this->urlHasFragment($url)) {
            throw new InvalidUrlException(
                \sprintf('Given baseUrl "%s" must not have a fragment!', $url),
                1586539165
            );
        }

        if ($this->urlHasUserInfo($url)) {
            throw new InvalidUrlException(
                \sprintf('Given baseUrl "%s" must not have a user info!', $url),
                1586539334
            );
        }
    }

    private function isValidUrl(string $url): bool
    {
        return \filter_var(
            $url,
            \FILTER_VALIDATE_URL,
            \FILTER_FLAG_PATH_REQUIRED
        ) !== false;
    }

    private function urlHasHttpProtocolScheme(string $url): bool
    {
        return \str_contains($url, 'http:') || \str_contains($url, 'https:');
    }

    private function urlHasQueryParameters(string $url): bool
    {
        return \str_contains($url, '?');
    }

    private function urlHasFragment(string $url): bool
    {
        return \str_contains($url, '#');
    }

    private function urlHasUserInfo(string $url): bool
    {
        return \str_contains($url, '@');
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getApiUrl(): string
    {
        return $this->getBaseUrl() . static::API_ENDPOINT;
    }

    public function getResourceUrl(string $resource): string
    {
        return $this->getApiUrl() . \ltrim($resource, '/');
    }

    public function __toString()
    {
        return $this->getBaseUrl();
    }
}
