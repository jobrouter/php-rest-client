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

namespace Brotkrueml\JobRouterClient\Tests\Unit\Resource;

use Brotkrueml\JobRouterClient\Exception\InvalidUrlException;
use Brotkrueml\JobRouterClient\Resource\JobRouterInstallation;
use PHPUnit\Framework\TestCase;

class JobRouterInstallationTest extends TestCase
{
    /**
     * @test
     * @dataProvider dataProviderForGetBaseUrl
     * @param string $urlToTest
     * @param string $expectedUrl
     */
    public function getBaseUrlReturnsBaseUrlCorrectly(string $urlToTest, string $expectedUrl): void
    {
        $subject = new JobRouterInstallation($urlToTest);

        self::assertSame($expectedUrl, $subject->getBaseUrl());
    }

    public function dataProviderForGetBaseUrl(): \Generator
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

    /**
     * @test
     * @dataProvider dataProviderForGetApiUrl
     * @param string $urlToTest
     * @param string $expectedUrl
     */
    public function getApiUrlReturnsApiUrlCorrectly(string $urlToTest, string $expectedUrl): void
    {
        $subject = new JobRouterInstallation($urlToTest);

        self::assertSame($expectedUrl, $subject->getApiUrl());
    }

    public function dataProviderForGetApiUrl(): \Generator
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

    /**
     * @test
     */
    public function toStringReturnsBaseUrl(): void
    {
        $subject = new JobRouterInstallation('https://example.org/foo/');

        self::assertSame('https://example.org/foo/', $subject->__toString());
    }

    /**
     * @test
     * @dataProvider dataProviderForGetResourceUrl
     * @param string $givenUrl
     * @param string $resourceToTest
     * @param string $expectedUrl
     */
    public function getResourceUrlReturnsApiUrlCorrectly(
        string $givenUrl,
        string $resourceToTest,
        string $expectedUrl
    ): void {
        $subject = new JobRouterInstallation($givenUrl);

        self::assertSame($expectedUrl, $subject->getResourceUrl($resourceToTest));
    }

    public function dataProviderForGetResourceUrl(): \Generator
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

    /**
     * @test
     * @dataProvider dataProviderForInvalidUrls
     * @param string $url
     * @param string $expectedExceptionMessage
     * @param int $expectedExceptionCode
     */
    public function constructThrowsExceptionOnInvalidUrl(
        string $url,
        string $expectedExceptionMessage,
        int $expectedExceptionCode
    ): void {
        $this->expectException(InvalidUrlException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->expectExceptionCode($expectedExceptionCode);

        new JobRouterInstallation($url);
    }

    public function dataProviderForInvalidUrls(): \Generator
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
