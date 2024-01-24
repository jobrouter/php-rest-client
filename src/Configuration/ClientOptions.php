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

/**
 * Value object with available client options which can be defined
 * @see \Buzz\Client\AbstractClient->configureOptions()
 */
final class ClientOptions
{
    public function __construct(
        private readonly bool $allowRedirects = false,
        private readonly int $maxRedirects = 5,
        private readonly int $timeout = 0,
        private readonly bool $verify = true,
        private readonly ?string $proxy = null,
    ) {}

    /**
     * @internal
     * @return array{
     *     allow_redirects: bool,
     *     max_redirects: int,
     *     timeout: int,
     *     verify: bool,
     *     proxy: string|null
     * }
     */
    public function toArray(): array
    {
        /**
         * @phpstan-ignore-next-line
         */
        return [
            'allow_redirects' => $this->allowRedirects,
            'max_redirects' => $this->maxRedirects,
            'timeout' => $this->timeout,
            'verify' => $this->verify,
            'proxy' => $this->proxy,
        ];
    }
}
