<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Tests\Unit\Resource;

use JobRouter\AddOn\RestClient\Exception\InvalidUrlException;
use JobRouter\AddOn\RestClient\Resource\JobRouterSystem;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class JobRouterSystemTest extends TestCase
{
    #[DataProvider('dataProviderForGetBaseUrl')]
    #[Test]
    public function getBaseUrlReturnsBaseUrlCorrectly(string $urlToTest, string $expectedUrl): void
    {
        $subject = new JobRouterSystem($urlToTest);

        self::assertSame($expectedUrl, $subject->getBaseUrl());
    }

    public static function dataProviderForGetBaseUrl(): \Iterator
    {
        yield 'Base URL with ending slash' => [
            'https://example.org/jobrouter/',
            'https://example.org/jobrouter/',
        ];

        yield 'Base URL without ending slash' => [
            'https://example.org/jobrouter',
            'https://example.org/jobrouter/',
        ];
    }

    #[DataProvider('dataProviderForGetApiUrl')]
    #[Test]
    public function getApiUrlReturnsApiUrlCorrectly(string $urlToTest, string $expectedUrl): void
    {
        $subject = new JobRouterSystem($urlToTest);

        self::assertSame($expectedUrl, $subject->getApiUrl());
    }

    public static function dataProviderForGetApiUrl(): \Iterator
    {
        yield 'Base URL with ending slash' => [
            'https://example.org/jobrouter/',
            'https://example.org/jobrouter/api/rest/v2/',
        ];

        yield 'Base URL without ending slash' => [
            'https://example.org/jobrouter',
            'https://example.org/jobrouter/api/rest/v2/',
        ];
    }

    #[Test]
    public function toStringReturnsBaseUrl(): void
    {
        $subject = new JobRouterSystem('https://example.org/foo/');

        self::assertSame('https://example.org/foo/', $subject->__toString());
    }

    #[DataProvider('dataProviderForGetResourceUrl')]
    #[Test]
    public function getResourceUrlReturnsApiUrlCorrectly(
        string $givenUrl,
        string $resourceToTest,
        string $expectedUrl,
    ): void {
        $subject = new JobRouterSystem($givenUrl);

        self::assertSame($expectedUrl, $subject->getResourceUrl($resourceToTest));
    }

    public static function dataProviderForGetResourceUrl(): \Iterator
    {
        yield 'Resource with leading slash' => [
            'https://example.org/',
            '/application/tokens',
            'https://example.org/api/rest/v2/application/tokens',
        ];

        yield 'Resource without leading slash' => [
            'https://example.org/',
            'application/tokens',
            'https://example.org/api/rest/v2/application/tokens',
        ];
    }

    #[DataProvider('dataProviderForInvalidUrls')]
    #[Test]
    public function constructThrowsExceptionOnInvalidUrl(
        string $url,
        string $expectedExceptionMessage,
        int $expectedExceptionCode,
    ): void {
        $this->expectException(InvalidUrlException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->expectExceptionCode($expectedExceptionCode);

        new JobRouterSystem($url);
    }

    public static function dataProviderForInvalidUrls(): \Iterator
    {
        yield 'Random string' => [
            'some random string',
            'Given baseUrl "some random string" is not valid. It must consist of a scheme, the host and the path!',
            1565710531,
        ];

        yield 'URL does not provide a path' => [
            'https://example.org',
            'Given baseUrl "https://example.org" is not valid. It must consist of a scheme, the host and the path!',
            1565710531,
        ];

        yield 'URL not starting with http protocol scheme' => [
            'ftp://some.host/',
            'Given baseUrl "ftp://some.host/" must have a HTTP protocol scheme!',
            1586538201,
        ];

        yield 'URL has a query parameter' => [
            'http://example.org/?bar=foo',
            'Given baseUrl "http://example.org/?bar=foo" must not have query parameters!',
            1586538700,
        ];

        yield 'URL has a fragment' => [
            'https://example.org/#foo',
            'Given baseUrl "https://example.org/#foo" must not have a fragment!',
            1586539165,
        ];

        yield 'URL has a user info' => [
            'https://user:pass@example.org/',
            'Given baseUrl "https://user:pass@example.org/" must not have a user info!',
            1586539334,
        ];
    }
}
