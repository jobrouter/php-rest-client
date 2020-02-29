<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Resource;

final class FileStorage implements \Countable, \Iterator
{
    /**
     * @var array
     */
    private $files = [];

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return \count($this->files);
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return \current($this->files);
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        \next($this->files);
    }

    /**
     * @inheritDoc
     */
    public function key(): string
    {
        return \key($this->files);
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return \current($this->files) !== false;
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        \reset($this->files);
    }

    /**
     * Adds a file to the storage
     *
     * @param FileInterface $file
     */
    public function attach(FileInterface $file): void
    {
        $this->files[\spl_object_hash($file)] = $file;
    }

    /**
     * Removes a file from the storage
     *
     * @param FileInterface $file
     */
    public function detach(FileInterface $file): void
    {
        unset($this->files[\spl_object_hash($file)]);
    }

    /**
     * Checks if the storage contains a specific file
     *
     * @param FileInterface $file
     * @return bool
     */
    public function contains(FileInterface $file): bool
    {
        return isset($this->files[\spl_object_hash($file)]);
    }
}
