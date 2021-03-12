<?php

/**
 * This file is part of the JobRouter Client.
 *
 * Copyright (c) 2019-2021 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 *
 * @see https://github.com/brotkrueml/jobrouter-client
 */

namespace Brotkrueml\JobRouterClient\Tests\Unit\Mapper;

use Brotkrueml\JobRouterClient\Mapper\RouteContentTypeMapper;
use PHPUnit\Framework\TestCase;

class RouteContentTypeMapperTest extends TestCase
{
    /** @var RouteContentTypeMapper */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new RouteContentTypeMapper();
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param string $method
     * @param string $resource
     * @param string $expectedContentType
     */
    public function getRequestContentTypeForRouteReturnsCorrectContentType(
        string $method,
        string $resource,
        string $expectedContentType
    ): void {
        $actual = $this->subject->getRequestContentTypeForRoute($method, $resource);

        self::assertSame($expectedContentType, $actual);
    }

    /**
     * @return iterable
     */
    public function dataProvider(): iterable
    {
        $handle = \fopen(__DIR__ . DIRECTORY_SEPARATOR . 'routes.txt', 'r');

        while (($line = \fgets($handle, 1024)) !== false) {
            $line = \trim($line);

            if (empty($line) || \str_starts_with($line, '#')) {
                continue;
            }

            [$resource, $method, $contentType] = \explode(' ', $line);

            /** @noinspection PhpUnnecessaryLocalVariableInspection */
            $description = \sprintf(
                '%s %s returns %s',
                $method,
                $resource,
                $contentType === '-' ? 'empty content type' : $contentType
            );

            yield $description => [
                $method,
                \ltrim($resource, '/'),
                $contentType === '-' ? '' : $contentType
            ];
        }

        \fclose($handle);
    }
}
