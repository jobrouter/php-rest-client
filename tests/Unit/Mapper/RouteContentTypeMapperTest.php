<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Tests\Unit\Mapper;

use JobRouter\AddOn\RestClient\Mapper\RouteContentTypeMapper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RouteContentTypeMapperTest extends TestCase
{
    private RouteContentTypeMapper $subject;

    protected function setUp(): void
    {
        $this->subject = new RouteContentTypeMapper();
    }

    #[DataProvider('dataProvider')]
    #[Test]
    public function getRequestContentTypeForRouteReturnsCorrectContentType(
        string $method,
        string $resource,
        string $expectedContentType,
    ): void {
        $actual = $this->subject->getRequestContentTypeForRoute($method, $resource);

        self::assertSame($expectedContentType, $actual);
    }

    public static function dataProvider(): iterable
    {
        $handle = \fopen(__DIR__ . \DIRECTORY_SEPARATOR . 'routes.txt', 'r');

        while (($line = \fgets($handle, 1024)) !== false) {
            $line = \trim($line);
            if ($line === '') {
                continue;
            }
            if (\str_starts_with($line, '#')) {
                continue;
            }

            [$resource, $method, $contentType] = \explode(' ', $line);

            $description = \sprintf(
                '%s %s returns %s',
                $method,
                $resource,
                $contentType === '-' ? 'empty content type' : $contentType,
            );

            yield $description => [
                $method,
                \ltrim($resource, '/'),
                $contentType === '-' ? '' : $contentType,
            ];
        }

        \fclose($handle);
    }
}
