<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter Client.
 * https://github.com/brotkrueml/jobrouter-client
 *
 * Copyright (c) 2019-2022 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterClient\Model;

use Brotkrueml\JobRouterClient\Enumerations\Priority;
use Brotkrueml\JobRouterClient\Exception\InvalidPoolNumberException;
use Brotkrueml\JobRouterClient\Exception\InvalidStepNumberException;
use Brotkrueml\JobRouterClient\Resource\FileInterface;

final class Incident
{
    private const DEFAULT_POOL = 1;

    private int $step;
    private string $initiator = '';
    private string $username = '';
    private string $jobFunction = '';
    private string $summary = '';
    private Priority $priority = Priority::Normal;
    private int $pool = self::DEFAULT_POOL;
    private bool $simulation = false;
    private ?\DateTimeInterface $stepEscalationDate = null;
    private ?\DateTimeInterface $incidentEscalationDate = null;
    /**
     * @var array<string,string|int|bool|FileInterface>
     */
    private array $processTableFields = [];
    /**
     * @var array<string,list<array<string, string|int|bool|FileInterface>>>
     */
    private array $subTables = [];

    public function __construct(int $step)
    {
        $this->setStep($step);
    }

    public function getStep(): int
    {
        return $this->step;
    }

    /**
     * @throws InvalidStepNumberException
     */
    public function setStep(int $step): self
    {
        if ($step <= 0) {
            throw InvalidStepNumberException::forStepNumber($step);
        }

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

    public function getPriority(): Priority
    {
        return $this->priority;
    }

    public function setPriority(Priority $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getPool(): int
    {
        return $this->pool;
    }

    /**
     * @throws InvalidPoolNumberException
     */
    public function setPool(int $pool): self
    {
        if ($pool < 1) {
            throw InvalidPoolNumberException::forPoolNumber($pool);
        }

        $this->pool = $pool;

        return $this;
    }

    public function isSimulation(): bool
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
     * @return array<string,string|int|bool|FileInterface>
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

    public function setProcessTableField(string $name, string|int|bool|FileInterface $value): self
    {
        $this->processTableFields[$name] = $value;

        return $this;
    }

    /**
     * @param list<array<string, string|int|bool|FileInterface>> $rows
     */
    public function setRowsForSubTable(string $subTableName, array $rows): self
    {
        $this->subTables[$subTableName] = $rows;

        return $this;
    }

    /**
     * @param array<string, string|int|bool|FileInterface> $row
     */
    public function addRowToSubTable(string $subTableName, array $row): self
    {
        $this->subTables[$subTableName][] = $row;

        return $this;
    }

    /**
     * @return list<array<string, string|int|bool|FileInterface>>|null
     */
    public function getRowsForSubTable(string $subTableName): ?array
    {
        return $this->subTables[$subTableName] ?? null;
    }

    /**
     * @return array<string, list<array<string, string|int|bool|FileInterface>>>
     * @internal
     */
    public function getSubTables(): array
    {
        return $this->subTables;
    }
}
