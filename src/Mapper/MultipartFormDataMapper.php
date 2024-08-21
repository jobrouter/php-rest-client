<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Mapper;

use GuzzleHttp\Psr7\Utils;
use JobRouter\AddOn\RestClient\Exception\FileNotFoundException;
use JobRouter\AddOn\RestClient\Resource\FileInterface;

final class MultipartFormDataMapper
{
    /**
     * @param array<string, string|int|float|bool|FileInterface|array{path: non-empty-string, filename?: string, contentType?: string}> $data
     * @return list<array{name: string, contents: string|resource, filename? :string, headers? :array{Content-Type: string}}>
     */
    public function map(array $data): array
    {
        return \array_map(static function (string $name, string|int|float|bool|FileInterface|array $value): array {
            if ($value instanceof FileInterface) {
                $value = $value->toArray();
            }
            if (\is_array($value)) {
                // @phpstan-ignore-next-line  Offset 'path' always exists and is not nullable.
                if (! isset($value['path'])) {
                    throw FileNotFoundException::fromEmptyPath();
                }

                try {
                    $multipart = [
                        'name' => $name,
                        'contents' => Utils::tryFopen($value['path'], 'r'),
                    ];
                } catch (\RuntimeException $e) {
                    throw FileNotFoundException::fromPath($value['path'], $e);
                }
                if (($value['filename'] ?? '') !== '') {
                    $multipart['filename'] = $value['filename'];
                }
                if (($value['contentType'] ?? '') !== '') {
                    $multipart['headers'] = [
                        'Content-Type' => $value['contentType'],
                    ];
                }

                return $multipart;
            }

            // @phpstan-ignore-next-line Use value object over return of values
            return [
                'name' => $name,
                'contents' => (string)$value,
            ];
        }, \array_keys($data), \array_values($data));
    }
}
