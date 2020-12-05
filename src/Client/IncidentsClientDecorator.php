<?php
declare(strict_types=1);

/**
 * This file is part of the JobRouter Client.
 *
 * Copyright (c) 2019-2020 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 *
 * @see https://github.com/brotkrueml/jobrouter-client
 */

namespace Brotkrueml\JobRouterClient\Client;

use Brotkrueml\JobRouterClient\Model\Incident;
use Brotkrueml\JobRouterClient\Resource\FileInterface;
use Psr\Http\Message\ResponseInterface;

final class IncidentsClientDecorator extends ClientDecorator
{
    public function request(string $method, string $resource, $data = []): ResponseInterface
    {
        if ($data instanceof Incident) {
            return $this->client->request($method, $resource, $this->buildMultipart($data));
        }

        return $this->client->request($method, $resource, $data);
    }

    private function buildMultipart(Incident $incident): array
    {
        $multipart = [];

        if (!empty($incident->getStep())) {
            $multipart['step'] = (string)$incident->getStep();
        }

        if (!empty($incident->getInitiator())) {
            $multipart['initiator'] = $incident->getInitiator();
        }

        if (!empty($incident->getUsername())) {
            $multipart['username'] = $incident->getUsername();
        }

        if (!empty($incident->getJobFunction())) {
            $multipart['jobfunction'] = $incident->getJobFunction();
        }

        if (!empty($incident->getSummary())) {
            $multipart['summary'] = $incident->getSummary();
        }

        if (!empty($incident->getPriority())) {
            $multipart['priority'] = (string)$incident->getPriority();
        }

        if (!empty($incident->getPool())) {
            $multipart['pool'] = (string)$incident->getPool();
        }

        if (!empty($incident->isSimulation())) {
            $multipart['simulation'] = (string)$incident->isSimulation();
        }

        if ($incident->getStepEscalationDate() instanceof \DateTimeInterface) {
            /** @psalm-suppress PossiblyNullReference */
            $multipart['step_escalation_date'] = $incident->getStepEscalationDate()->format('c');
        }

        if ($incident->getIncidentEscalationDate() instanceof \DateTimeInterface) {
            /** @psalm-suppress PossiblyNullReference */
            $multipart['incident_escalation_date'] = $incident->getIncidentEscalationDate()->format('c');
        }

        $multipartProcessTableFields = $this->buildProcessTableFieldsForMultipart(
            $incident->getProcessTableFields()
        );

        $multipartSubTables = $this->buildSubTablesForMultipart(
            $incident->getSubTables()
        );

        return \array_merge($multipart, $multipartProcessTableFields, $multipartSubTables);
    }

    /**
     * @param array<string,mixed> $processTableFields
     * @return array
     */
    private function buildProcessTableFieldsForMultipart(array $processTableFields): array
    {
        $multipartProcessTableFields = [];

        $index = 0;
        foreach ($processTableFields as $name => $value) {
            $multipartProcessTableFields[$this->getProcessTableFieldKey($index, 'name')] = $name;
            $multipartProcessTableFields[$this->getProcessTableFieldKey($index, 'value')]
                = $this->prepareFieldValue($value);
            $index++;
        }

        return $multipartProcessTableFields;
    }

    /**
     * @param bool|int|string|FileInterface $value
     * @return string|FileInterface
     */
    private function prepareFieldValue($value)
    {
        if (\is_bool($value)) {
            $value = (int)$value;
        }

        return $value instanceof FileInterface ? $value : (string)$value;
    }

    private function getProcessTableFieldKey(int $index, string $part): string
    {
        return \sprintf(
            'processtable[fields][%d][%s]',
            $index,
            $part
        );
    }

    private function buildSubTablesForMultipart(array $subTables): array
    {
        $multipartSubTables = [];

        $subTableIndex = 0;
        foreach ($subTables as $subTableName => $subTableRows) {
            $multipartSubTables[$this->getSubTableNameKey($subTableIndex)] = $subTableName;
            $rowIndex = 0;
            foreach ($subTableRows as $row) {
                $columnIndex = 0;
                foreach ($row as $columnName => $columnValue) {
                    $multipartSubTables[$this->getSubTableFieldKey($subTableIndex, $rowIndex, $columnIndex, 'name')]
                        = $columnName;
                    $multipartSubTables[$this->getSubTableFieldKey($subTableIndex, $rowIndex, $columnIndex, 'value')]
                        = $this->prepareFieldValue($columnValue);
                    $columnIndex++;
                }
                $rowIndex++;
            }
            $subTableIndex++;
        }

        return $multipartSubTables;
    }

    private function getSubTableNameKey(int $index): string
    {
        return \sprintf('subtables[%d][name]', $index);
    }

    private function getSubTableFieldKey(int $subTableIndex, int $rowIndex, int $columnIndex, string $part): string
    {
        return \sprintf(
            'subtables[%d][rows][%d][fields][%d][%s]',
            $subTableIndex,
            $rowIndex,
            $columnIndex,
            $part
        );
    }
}
