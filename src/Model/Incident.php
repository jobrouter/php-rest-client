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

final class Incident
{
    public const PRIORITY_LOW = 1;
    /**
     * @noRector \Rector\DeadCode\Rector\ClassConst\RemoveUnusedClassConstantRector
     */
    public const PRIORITY_NORMAL = 2;
    public const PRIORITY_HIGH = 3;

    /**
     * @var int|null
     */
    private $step;

    /**
     * @var string
     */
    private $initiator = '';

    /**
     * @var string
     */
    private $username = '';

    /**
     * @var string
     */
    private $jobFunction = '';

    /**
     * @var string
     */
    private $summary = '';

    /**
     * @var int|null
     */
    private $priority;

    /**
     * @var int|null
     */
    private $pool;

    /**
     * @var bool|null
     */
    private $simulation;

    /**
     * @var \DateTimeInterface|null
     */
    private $stepEscalationDate;

    /**
     * @var \DateTimeInterface|null
     */
    private $incidentEscalationDate;

    /**
     * @var array<string,string|int|bool|FileInterface>
     */
    private $processTableFields = [];

    /**
     * @var array<string,array>
     */
    private $subTables = [];

    public function getStep(): ?int
    {
        return $this->step;
    }

    public function setStep(int $step): self
    {
        $this->step = $step;

        return $this;
    }

    public function getInitiator(): string
    {
        return $this->initiator;
    }

    public function setInitiator(string $initiator): self
    {
        $this->initiator = $initiator;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getJobFunction(): string
    {
        return $this->jobFunction;
    }

    public function setJobFunction(string $jobFunction): self
    {
        $this->jobFunction = $jobFunction;

        return $this;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * @psalm-param self::PRIORITY_LOW|self::PRIORITY_NORMAL|self::PRIORITY_HIGH $priority
     * @throws \InvalidArgumentException
     */
    public function setPriority(int $priority): self
    {
        if ($priority < self::PRIORITY_LOW || $priority > self::PRIORITY_HIGH) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'proprity has to be an integer between 1 and 3, "%d" given',
                    $priority
                ),
                1578225130
            );
        }

        $this->priority = $priority;

        return $this;
    }

    public function getPool(): ?int
    {
        return $this->pool;
    }

    public function setPool(int $pool): self
    {
        if ($pool < 1) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'pool must be a positive integer, "%d" given',
                    $pool
                ),
                1578228017
            );
        }

        $this->pool = $pool;

        return $this;
    }

    public function isSimulation(): ?bool
    {
        return $this->simulation;
    }

    public function setSimulation(bool $simulation): self
    {
        $this->simulation = $simulation;

        return $this;
    }

    public function getStepEscalationDate(): ?\DateTimeInterface
    {
        return $this->stepEscalationDate;
    }

    public function setStepEscalationDate(\DateTimeInterface $stepEscalationDate): self
    {
        $this->stepEscalationDate = $stepEscalationDate;

        return $this;
    }

    public function getIncidentEscalationDate(): ?\DateTimeInterface
    {
        return $this->incidentEscalationDate;
    }

    public function setIncidentEscalationDate(\DateTimeInterface $incidentEscalationDate): self
    {
        $this->incidentEscalationDate = $incidentEscalationDate;

        return $this;
    }

    /**
     * @internal
     */
    public function getProcessTableFields(): array
    {
        return $this->processTableFields;
    }

    /**
     * @return string|int|bool|FileInterface|null
     */
    public function getProcessTableField(string $name)
    {
        return $this->processTableFields[$name] ?? null;
    }

    /**
     * @param string|int|bool|FileInterface $value
     * @throws \InvalidArgumentException
     * @psalm-suppress DocblockTypeContradiction
     */
    public function setProcessTableField(string $name, $value): self
    {
        if (! \is_string($value) && ! \is_int($value) && ! \is_bool($value) && ! $value instanceof FileInterface) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'value has to be either a string, an integer, a boolean or an instance of %s, "%s" given',
                    FileInterface::class,
                    \get_debug_type($value)
                ),
                1578225863
            );
        }

        $this->processTableFields[$name] = $value;

        return $this;
    }

    public function setRowsForSubTable(string $subTableName, array $rows): self
    {
        $this->subTables[$subTableName] = $rows;

        return $this;
    }

    public function addRowToSubTable(string $subTableName, array $row): self
    {
        $this->subTables[$subTableName][] = $row;

        return $this;
    }

    public function getRowsForSubTable(string $subTableName): ?array
    {
        return $this->subTables[$subTableName] ?? null;
    }

    /**
     * @internal
     */
    public function getSubTables(): array
    {
        return $this->subTables;
    }
}
