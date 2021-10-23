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

namespace Brotkrueml\JobRouterClient\Resource;

/**
 * @implements \Iterator<FileInterface>
 */
final class FileStorage implements \Countable, \Iterator
{
    /**
     * @var array<string,FileInterface>
     */
    private $files = [];

    public function count(): int
    {
        return \count($this->files);
    }

    /**
     * @return FileInterface|false
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return \current($this->files);
    }

    public function next(): void
    {
        \next($this->files);
    }

    public function key(): ?string
    {
        return \key($this->files);
    }

    public function valid(): bool
    {
        return \current($this->files) !== false;
    }

    public function rewind(): void
    {
        \reset($this->files);
    }

    /**
     * Adds a file to the storage
     */
    public function attach(FileInterface $file): void
    {
        $this->files[\spl_object_hash($file)] = $file;
    }

    /**
     * Removes a file from the storage
     */
    public function detach(FileInterface $file): void
    {
        unset($this->files[\spl_object_hash($file)]);
    }

    /**
     * Checks if the storage contains a specific file
     */
    public function contains(FileInterface $file): bool
    {
        return isset($this->files[\spl_object_hash($file)]);
    }
}
