<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Client;

use JobRouter\AddOn\RestClient\Exception\HttpException;
use JobRouter\AddOn\RestClient\Model\Document;
use JobRouter\AddOn\RestClient\Resource\FileInterface;
use JobRouter\AddOn\RestClient\Resource\FileStorage;
use Psr\Http\Message\ResponseInterface;

final class DocumentsClientDecorator extends ClientDecorator
{
    /**
     * Send a request to the configured JobRouter system
     *
     * @param string $method The method
     * @param string $resource The resource path
     * @param array<string,mixed>|Document $data Data for the request
     *
     * @throws HttpException
     */
    public function request(string $method, string $resource, $data = []): ResponseInterface
    {
        if ($data instanceof Document) {
            return $this->client->request($method, $resource, $this->buildMultipart($data));
        }

        return $this->client->request($method, $resource, $data);
    }

    /**
     * @return array<string, string|int|FileInterface>
     */
    private function buildMultipart(Document $document): array
    {
        $multipartIndexFields = $this->buildFieldsForMultipart('index', $document->getIndexFields());
        $multipartKeywordFields = $this->buildFieldsForMultipart('keyword', $document->getKeywordFields());
        $multipartFiles = $this->buildFilesForMultipart($document->getFiles());

        // @phpstan-ignore-next-line Use value object over return of values
        return [...$multipartIndexFields, ...$multipartKeywordFields, ...$multipartFiles];
    }

    /**
     * @param array<string, string|int> $fields
     * @return array<string, string|int>
     */
    private function buildFieldsForMultipart(string $type, array $fields): array
    {
        $multipartFields = [];

        $index = 0;
        foreach ($fields as $name => $value) {
            $multipartFields[$this->getFieldKey($type, $index, 'name')] = $name;
            $multipartFields[$this->getFieldKey($type, $index, 'value')] = $value;
            $index++;
        }

        return $multipartFields;
    }

    private function getFieldKey(string $type, int $index, string $part): string
    {
        return \sprintf(
            '%sFields[%d][%s]',
            $type,
            $index,
            $part,
        );
    }

    /**
     * @return array<string, FileInterface>
     */
    private function buildFilesForMultipart(FileStorage $files): array
    {
        $multipartFiles = [];

        $index = 0;
        foreach ($files as $file) {
            /** @var FileInterface $file */
            $multipartFiles[\sprintf('files[%d]', $index)] = $file;
            $index++;
        }

        return $multipartFiles;
    }
}
