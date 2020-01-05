<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Client;

use Brotkrueml\JobRouterClient\Model\Incident;
use Psr\Http\Message\ResponseInterface;

/**
 * RestClient for handling HTTP requests
 */
final class IncidentsClient implements ClientInterface
{
    /**
     * @var RestClient
     */
    private $client;

    public function __construct(RestClient $client)
    {
        $this->client = $client;
    }

    public function authenticate(): void
    {
        $this->client->authenticate();
    }

    public function request(string $method, string $resource, $data = []): ResponseInterface
    {
        if (!$data instanceof Incident) {
            return $this->client->request($method, $resource, $data);
        }

        return $this->client->request($method, $resource, ['multipart' => $this->buildMultipart($data)]);
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

        if (!empty($incident->getJobfunction())) {
            $multipart['jobfunction'] = $incident->getJobfunction();
        }

        if (!empty($incident->getJobfunction())) {
            $multipart['jobfunction'] = $incident->getJobfunction();
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

        if (!empty($incident->getSimulation())) {
            $multipart['simulation'] = (bool)$incident->getPool();
        }

        if ($incident->getStepEscalationDate() instanceof \DateTime) {
            /** @psalm-suppress PossiblyNullReference */
            $multipart['step_escalation_date'] = $incident->getStepEscalationDate()->format('c');
        }

        if ($incident->getIncidentEscalationDate() instanceof \DateTime) {
            /** @psalm-suppress PossiblyNullReference */
            $multipart['incident_escalation_date'] = $incident->getIncidentEscalationDate()->format('c');
        }

        $multipartProcessTableFields = $this->buildProcessTableFieldsForMultipart(
            $incident->getProcessTableFields()
        );

        $multipartSubTables = $this->buildSubTablesForMultipart(
            $incident->getSubtables()
        );

        $multipart = \array_merge($multipart, $multipartProcessTableFields, $multipartSubTables);

        return $multipart;
    }

    private function buildProcessTableFieldsForMultipart(array $processTableFields): array
    {
        $multipartProcessTableFields = [];

        $index = 0;
        foreach ($processTableFields as $name => $value) {
            $multipartProcessTableFields[$this->getProcessTableFieldKey($index, 'name')]
                = $name;
            $multipartProcessTableFields[$this->getProcessTableFieldKey($index, 'value')]
                = $value;
            $index++;
        }

        return $multipartProcessTableFields;
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
                    $multipartSubTables[$this->getSubTableFieldKey($subTableIndex, $rowIndex, $columnIndex, 'name')] = $columnName;
                    $multipartSubTables[$this->getSubTableFieldKey($subTableIndex, $rowIndex, $columnIndex, 'value')] = $columnValue;
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
