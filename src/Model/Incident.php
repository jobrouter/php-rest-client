<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Model;

final class Incident
{
    public const PRIORITY_LOW = 1;
    public const PRIORITY_NORMAL = 2;
    public const PRIORITY_HIGH = 3;

    private const ALLOWED_FILE_VALUE_KEYS = ['path', 'filename', 'contentType'];
    private const REQUIRED_FILE_VALUE_KEYS = ['path', 'filename'];

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
    private $jobfunction = '';

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
     * @var \DateTime|null
     */
    private $stepEscalationDate;

    /**
     * @var \DateTime|null
     */
    private $incidentEscalationDate;

    /**
     * @var array
     */
    private $processTableFields = [];

    /**
     * @var array
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

    public function getJobfunction(): string
    {
        return $this->jobfunction;
    }

    public function setJobfunction(string $jobfunction): self
    {
        $this->jobfunction = $jobfunction;

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
     * @param int $priority
     * @return $this
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

    public function getSimulation(): ?bool
    {
        return $this->simulation;
    }

    public function setSimulation(bool $simulation): self
    {
        $this->simulation = $simulation;

        return $this;
    }

    public function getStepEscalationDate(): ?\DateTime
    {
        return $this->stepEscalationDate;
    }

    public function setStepEscalationDate(\DateTime $stepEscalationDate): self
    {
        $this->stepEscalationDate = $stepEscalationDate;

        return $this;
    }

    public function getIncidentEscalationDate(): ?\DateTime
    {
        return $this->incidentEscalationDate;
    }

    public function setIncidentEscalationDate(\DateTime $incidentEscalationDate): self
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
     * @param string $name
     * @return string|array|null
     */
    public function getProcessTableField(string $name)
    {
        return $this->processTableFields[$name] ?? null;
    }

    /**
     * @param string $name
     * @param string|array $value
     * @return $this
     * @throws \InvalidArgumentException
     * @psalm-suppress DocblockTypeContradiction
     */
    public function setProcessTableField(string $name, $value): self
    {
        if (!\is_string($value) && !\is_int($value) && !\is_array($value)) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'value has to be either a string, an integer or an array, "%s" given',
                    gettype($value)
                ),
                1578225863
            );
        }

        if (\is_array($value)) {
            $this->checkArrayKeysOfFileValue($name, $value);
        }

        $this->processTableFields[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @param array $value
     * @throws \InvalidArgumentException
     */
    private function checkArrayKeysOfFileValue(string $name, array $value): void
    {
        $disallowedKeys = \array_filter($value, function (string $key): bool {
            return !\in_array($key, self::ALLOWED_FILE_VALUE_KEYS);
        }, \ARRAY_FILTER_USE_KEY);

        if (!empty($disallowedKeys)) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'The following value keys for the process table field "%s" are not allowed: %s',
                    $name,
                    \implode(', ', \array_keys($disallowedKeys))
                ),
                1578226362
            );
        }

        $missingRequiredKeys = [];
        $valueKeys = \array_keys($value);
        foreach (self::REQUIRED_FILE_VALUE_KEYS as $requiredKey) {
            if (!\in_array($requiredKey, $valueKeys)) {
                $missingRequiredKeys[] = $requiredKey;
            }
        }

        if (!empty($missingRequiredKeys)) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'The following value keys for the process table field "%s" are required: %s',
                    $name,
                    \implode(', ', $missingRequiredKeys)
                ),
                1578226363
            );
        }
    }

    public function setRowsForSubTable(string $subTableName, array $data): self
    {
        $this->subTables[$subTableName] = $data;

        return $this;
    }

    public function addRowToSubTable(string $subTableName, array $rowData): self
    {
        $this->subTables[$subTableName][] = $rowData;

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
