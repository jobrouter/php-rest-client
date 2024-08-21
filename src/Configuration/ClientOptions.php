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
 * @see https://docs.guzzlephp.org/en/stable/request-options.html
 */
final class ClientOptions
{
    public function __construct(
        private readonly bool $allowRedirects = false,
        private readonly int $maxRedirects = 5,
        private readonly int|float $timeout = 0,
        private readonly bool $verify = true,
        private readonly ?string $proxy = null,
    ) {}

    /**
     * @internal
     * @return array{
     *     allow_redirects?: bool|array{max: int},
     *     timeout: int|float,
     *     verify: bool,
     *     proxy: string|null
     * }
     */
    public function toArray(): array
    {
        $allowRedirects = $this->allowRedirects ? [
            'max' => $this->maxRedirects,
        ] : false;

        /**
         * @phpstan-ignore-next-line Use value object over return of values
         */
        return [
            'allow_redirects' => $allowRedirects,
            'timeout' => $this->timeout,
            'verify' => $this->verify,
            'proxy' => $this->proxy,
        ];
    }
}
