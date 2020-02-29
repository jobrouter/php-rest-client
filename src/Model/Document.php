<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Model;

use Brotkrueml\JobRouterClient\Resource\FileInterface;
use Brotkrueml\JobRouterClient\Resource\FileStorage;

final class Document
{
    /**
     * @var array
     */
    private $indexFields = [];

    /**
     * @var array
     */
    private $keywordFields = [];

    /**
     * @var FileStorage
     */
    private $fileStorage;

    public function __construct()
    {
        $this->fileStorage = new FileStorage();
    }

    /**
     * Get the value of an index field, null if not existing
     *
     * @param string $name
     * @return string|null
     */
    public function getIndexField(string $name): ?string
    {
        return $this->indexFields[$name] ?? null;
    }

    /**
     * Set the value of an index field
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setIndexField(string $name, string $value): self
    {
        $this->indexFields[$name] = $value;

        return $this;
    }

    /**
     * Get the values of keyword field, null if not existing
     *
     * @param string $name
     * @return string|null
     */
    public function getKeywordField(string $name): ?string
    {
        return $this->keywordFields[$name] ?? null;
    }

    /**
     * Set the value of a keyword field
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setKeywordField(string $name, string $value): self
    {
        $this->keywordFields[$name] = $value;

        return $this;
    }

    public function setFiles(FileStorage $fileStorage): self
    {
        $this->fileStorage = $fileStorage;

        return $this;
    }

    public function addFile(FileInterface $file): self
    {
        $this->fileStorage->attach($file);

        return $this;
    }

    public function getFiles(): FileStorage
    {
        return $this->fileStorage;
    }

    /**
     * @internal
     */
    public function getIndexFields(): array
    {
        return $this->indexFields;
    }

    /**
     * @internal
     */
    public function getKeywordFields(): array
    {
        return $this->keywordFields;
    }
}
