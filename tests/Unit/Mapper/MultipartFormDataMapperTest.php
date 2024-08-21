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

use JobRouter\AddOn\RestClient\Exception\FileNotFoundException;
use JobRouter\AddOn\RestClient\Mapper\MultipartFormDataMapper;
use JobRouter\AddOn\RestClient\Resource\File;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MultipartFormDataMapperTest extends TestCase
{
    #[Test]
    #[DataProvider('providerForMapScalarValuesCorrectly')]
    public function mapScalarValuesCorrectly(array $data, array $expected): void
    {
        $subject = new MultipartFormDataMapper();

        $actual = $subject->map($data);

        self::assertSame($expected, $actual);
    }

    public static function providerForMapScalarValuesCorrectly(): iterable
    {
        yield 'empty array' => [
            'data' => [],
            'expected' => [],
        ];

        yield 'with string value' => [
            'data' => [
                'processtable[fields][0][name]' => 'SOME_FIELD',
                'processtable[fields][0][value]' => 'some value',
            ],
            'expected' => [
                [
                    'name' => 'processtable[fields][0][name]',
                    'contents' => 'SOME_FIELD',
                ],
                [
                    'name' => 'processtable[fields][0][value]',
                    'contents' => 'some value',
                ],
            ],
        ];

        yield 'with int value' => [
            'data' => [
                'processtable[fields][0][name]' => 'SOME_FIELD',
                'processtable[fields][0][value]' => 42,
            ],
            'expected' => [
                [
                    'name' => 'processtable[fields][0][name]',
                    'contents' => 'SOME_FIELD',
                ],
                [
                    'name' => 'processtable[fields][0][value]',
                    'contents' => '42',
                ],
            ],
        ];

        yield 'with float value' => [
            'data' => [
                'processtable[fields][0][name]' => 'SOME_FIELD',
                'processtable[fields][0][value]' => 42.42,
            ],
            'expected' => [
                [
                    'name' => 'processtable[fields][0][name]',
                    'contents' => 'SOME_FIELD',
                ],
                [
                    'name' => 'processtable[fields][0][value]',
                    'contents' => '42.42',
                ],
            ],
        ];

        yield 'with true value' => [
            'data' => [
                'processtable[fields][0][name]' => 'SOME_FIELD',
                'processtable[fields][0][value]' => true,
            ],
            'expected' => [
                [
                    'name' => 'processtable[fields][0][name]',
                    'contents' => 'SOME_FIELD',
                ],
                [
                    'name' => 'processtable[fields][0][value]',
                    'contents' => '1',
                ],
            ],
        ];

        yield 'with false value' => [
            'data' => [
                'processtable[fields][0][name]' => 'SOME_FIELD',
                'processtable[fields][0][value]' => false,
            ],
            'expected' => [
                [
                    'name' => 'processtable[fields][0][name]',
                    'contents' => 'SOME_FIELD',
                ],
                [
                    'name' => 'processtable[fields][0][value]',
                    'contents' => '',
                ],
            ],
        ];
    }

    #[Test]
    #[DataProvider('providerForMapFileCorrectly')]
    public function mapFileCorrectly(
        array $data,
        string $expectedNameName,
        string $expectedNameContents,
        string $expectedValueName,
        array $expectedValueContentsPart,
    ): void {
        $subject = new MultipartFormDataMapper();

        $actual = $subject->map($data);

        self::assertSame($expectedNameName, $actual[0]['name']);
        self::assertSame($expectedNameContents, $actual[0]['contents']);
        self::assertSame($expectedValueName, $actual[1]['name']);
        self::assertIsResource($actual[1]['contents']);

        if (isset($expectedValueContentsPart['filename'])) {
            self::assertSame($expectedValueContentsPart['filename'], $actual[1]['filename']);
        }
        if (isset($expectedValueContentsPart['headers'])) {
            self::assertSame($expectedValueContentsPart['headers'], $actual[1]['headers']);
        }
    }

    public static function providerForMapFileCorrectly(): iterable
    {
        yield 'with array and all keys given' => [
            'data' => [
                'processtable[fields][0][name]' => 'SOME_FIELD',
                'processtable[fields][0][value]' => [
                    'path' => __DIR__ . '/../../Fixture/some.txt',
                    'filename' => 'other.txt',
                    'contentType' => 'plain/text',
                ],
            ],
            'expectedNameName' => 'processtable[fields][0][name]',
            'expectedNameContents' => 'SOME_FIELD',
            'expectedValueName' => 'processtable[fields][0][value]',
            'expectedValueContentsPart' => [
                'filename' => 'other.txt',
                'headers' => [
                    'Content-Type' => 'plain/text',
                ],
            ],
        ];

        yield 'with array and only path given' => [
            'data' => [
                'processtable[fields][0][name]' => 'SOME_FIELD',
                'processtable[fields][0][value]' => [
                    'path' => __DIR__ . '/../../Fixture/some.txt',
                ],
            ],
            'expectedNameName' => 'processtable[fields][0][name]',
            'expectedNameContents' => 'SOME_FIELD',
            'expectedValueName' => 'processtable[fields][0][value]',
            'expectedValueContentsPart' => [],
        ];

        yield 'with File and all keys given' => [
            'data' => [
                'processtable[fields][0][name]' => 'SOME_FIELD',
                'processtable[fields][0][value]' => new File(
                    __DIR__ . '/../../Fixture/some.txt',
                    'other.txt',
                    'plain/text',
                ),
            ],
            'expectedNameName' => 'processtable[fields][0][name]',
            'expectedNameContents' => 'SOME_FIELD',
            'expectedValueName' => 'processtable[fields][0][value]',
            'expectedValueContentsPart' => [
                'filename' => 'other.txt',
                'headers' => [
                    'Content-Type' => 'plain/text',
                ],
            ],
        ];

        yield 'with File and only path given' => [
            'data' => [
                'processtable[fields][0][name]' => 'SOME_FIELD',
                'processtable[fields][0][value]' => new File(
                    __DIR__ . '/../../Fixture/some.txt',
                ),
            ],
            'expectedNameName' => 'processtable[fields][0][name]',
            'expectedNameContents' => 'SOME_FIELD',
            'expectedValueName' => 'processtable[fields][0][value]',
            'expectedValueContentsPart' => [],
        ];
    }

    #[Test]
    public function mapWithArrayAndNoPathKeyGiven(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionCode(1724230703);

        $data = [
            'processtable[fields][0][name]' => 'SOME_FIELD',
            'processtable[fields][0][value]' => [],
        ];

        $subject = new MultipartFormDataMapper();

        $subject->map($data);
    }

    #[Test]
    public function mapWithArrayAndNonExistingPathGiven(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionCode(1724230704);

        $data = [
            'processtable[fields][0][name]' => 'SOME_FIELD',
            'processtable[fields][0][value]' => [
                'path' => 'non_existing.txt',
            ],
        ];

        $subject = new MultipartFormDataMapper();

        $subject->map($data);
    }
}
