<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter Client.
 * https://github.com/brotkrueml/jobrouter-client
 *
 * Copyright (c) 2019-2021 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterClient\Model;

use Brotkrueml\JobRouterClient\Resource\FileInterface;
use Brotkrueml\JobRouterClient\Resource\FileStorage;

final class Document
{
    /**
     * @var array<string,string>
     */
    private $indexFields = [];

    /**
     * @var array<string,string>
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
     */
    public function getIndexField(string $name): ?string
    {
        return $this->indexFields[$name] ?? null;
    }

    /**
     * Set the value of an index field
     */
    public function setIndexField(string $name, string $value): self
    {
        $this->indexFields[$name] = $value;

        return $this;
    }

    /**
     * Get the values of keyword field, null if not existing
     */
    public function getKeywordField(string $name): ?string
    {
        return $this->keywordFields[$name] ?? null;
    }

    /**
     * Set the value of a keyword field
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
     * @return array<string,string>
     * @internal
     */
    public function getIndexFields(): array
    {
        return $this->indexFields;
    }

    /**
     * @return array<string,string>
     * @internal
     */
    public function getKeywordFields(): array
    {
        return $this->keywordFields;
    }
}
