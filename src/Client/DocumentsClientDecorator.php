<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Client;

use Brotkrueml\JobRouterClient\Model\Document;
use Brotkrueml\JobRouterClient\Resource\FileStorage;
use Psr\Http\Message\ResponseInterface;

final class DocumentsClientDecorator extends ClientDecorator
{
    public function request(string $method, string $resource, $data = []): ResponseInterface
    {
        if ($data instanceof Document) {
            return $this->client->request($method, $resource, $this->buildMultipart($data));
        }

        return $this->client->request($method, $resource, $data);
    }

    private function buildMultipart(Document $document): array
    {
        $multipartIndexFields = $this->buildFieldsForMultipart('index', $document->getIndexFields());
        $multipartKeywordFields = $this->buildFieldsForMultipart('keyword', $document->getKeywordFields());
        $multipartFiles = $this->buildFilesForMultipart($document->getFiles());

        return \array_merge($multipartIndexFields, $multipartKeywordFields, $multipartFiles);
    }

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
            $part
        );
    }

    private function buildFilesForMultipart(FileStorage $files): array
    {
        $multipartFiles = [];

        $index = 0;
        foreach ($files as $file) {
            $multipartFiles[\sprintf('files[%d]', $index)] = $file;
            $index++;
        }

        return $multipartFiles;
    }
}
