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

namespace Brotkrueml\JobRouterClient\Tests\Unit\Model;

use Brotkrueml\JobRouterClient\Enumerations\Priority;
use Brotkrueml\JobRouterClient\Model\Incident;
use Brotkrueml\JobRouterClient\Resource\FileInterface;
use PHPUnit\Framework\TestCase;

class IncidentTest extends TestCase
{
    private Incident $subject;

    protected function setUp(): void
    {
        $this->subject = new Incident();
    }

    /**
     * @test
     */
    public function stepIsNullWhenNotSet(): void
    {
        self::assertNull($this->subject->getStep());
    }

    /**
     * @test
     */
    public function initiatorIsAnEmptyStringWhenNotSet(): void
    {
        self::assertSame('', $this->subject->getInitiator());
    }

    /**
     * @test
     */
    public function usernameIsAnEmptyStringWhenNotSet(): void
    {
        self::assertSame('', $this->subject->getUsername());
    }

    /**
     * @test
     */
    public function jobFunctionIsAnEmptyStringWhenNotSet(): void
    {
        self::assertSame('', $this->subject->getJobFunction());
    }

    /**
     * @test
     */
    public function summaryIsAnEmptyStringWhenNotSet(): void
    {
        self::assertSame('', $this->subject->getSummary());
    }

    /**
     * @test
     */
    public function priorityIsNormalWhenNotExplicitlySet(): void
    {
        self::assertSame(Priority::Normal, $this->subject->getPriority());
    }

    /**
     * @test
     */
    public function poolIsNullWhenNotSet(): void
    {
        self::assertNull($this->subject->getPool());
    }

    /**
     * @test
     */
    public function simulationIsNullWhenNotSet(): void
    {
        self::assertNull($this->subject->isSimulation());
    }

    /**
     * @test
     */
    public function stepEscalationDateIsNullWhenNotSet(): void
    {
        self::assertNull($this->subject->getStepEscalationDate());
    }

    /**
     * @test
     */
    public function incidentEscalationDateIsNullWhenNotSet(): void
    {
        self::assertNull($this->subject->getIncidentEscalationDate());
    }

    /**
     * @test
     */
    public function processTableFieldsIsAnEmptyArrayWhenNotSet(): void
    {
        self::assertSame([], $this->subject->getProcessTableFields());
    }

    /**
     * @test
     */
    public function getSubtablesIsAnEmptyArrayWhenNotSet(): void
    {
        self::assertSame([], $this->subject->getSubTables());
    }

    /**
     * @test
     */
    public function setAndGetStepAreCorrectImplemented(): void
    {
        $actual = $this->subject->setStep(42);

        self::assertSame($this->subject, $actual);
        self::assertSame(42, $this->subject->getStep());
    }

    /**
     * @test
     */
    public function setAndGetInitiatorAreCorrectImplemented(): void
    {
        $actual = $this->subject->setInitiator('some initiator');

        self::assertSame($this->subject, $actual);
        self::assertSame('some initiator', $this->subject->getInitiator());
    }

    /**
     * @test
     */
    public function setAndGetUsernameAreCorrectImplemented(): void
    {
        $actual = $this->subject->setUsername('some username');

        self::assertSame($this->subject, $actual);
        self::assertSame('some username', $this->subject->getUsername());
    }

    /**
     * @test
     */
    public function setAndGetJobFunctionAreCorrectImplemented(): void
    {
        $actual = $this->subject->setJobFunction('some jobfunction');

        self::assertSame($this->subject, $actual);
        self::assertSame('some jobfunction', $this->subject->getJobFunction());
    }

    /**
     * @test
     */
    public function setAndGetSummaryAreCorrectImplemented(): void
    {
        $actual = $this->subject->setSummary('some summary');

        self::assertSame($this->subject, $actual);
        self::assertSame('some summary', $this->subject->getSummary());
    }

    /**
     * @test
     */
    public function setAndGetPriorityAreCorrectImplemented(): void
    {
        $actual = $this->subject->setPriority(Priority::High);

        self::assertSame($this->subject, $actual);
        self::assertSame(Priority::High, $this->subject->getPriority());
    }

    /**
     * @test
     */
    public function setAndGetPoolAreCorrectImplemented(): void
    {
        $actual = $this->subject->setPool(123);

        self::assertSame($this->subject, $actual);
        self::assertSame(123, $this->subject->getPool());
    }

    /**
     * @test
     */
    public function setPoolThrowsExceptionWhenZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1578228017);

        $this->subject->setPool(0);
    }

    /**
     * @test
     */
    public function setPoolAcceptsOne(): void
    {
        $exception = null;
        try {
            $this->subject->setPool(1);
        } catch (\InvalidArgumentException $exception) {
        }

        self::assertNull($exception, 'Unexpected InvalidArgumentException');
    }

    /**
     * @test
     */
    public function setAndIsSimulationAreCorrectImplemented(): void
    {
        $actual = $this->subject->setSimulation(true);

        self::assertSame($this->subject, $actual);
        self::assertTrue($this->subject->isSimulation());

        $this->subject->setSimulation(false);

        self::assertFalse($this->subject->isSimulation());
    }

    /**
     * @test
     */
    public function setAndGetStepEscalationDateAreCorrectImplemented(): void
    {
        $dateTime = new \DateTimeImmutable('2020-01-05T13:45:17+01:00');

        $actual = $this->subject->setStepEscalationDate($dateTime);

        self::assertSame($this->subject, $actual);
        self::assertSame($dateTime, $this->subject->getStepEscalationDate());
    }

    /**
     * @test
     */
    public function setAndGetIncidentEscalationDateAreCorrectImplemented(): void
    {
        $dateTime = new \DateTimeImmutable('2020-01-05T13:46:43+01:00');

        $actual = $this->subject->setIncidentEscalationDate($dateTime);

        self::assertSame($this->subject, $actual);
        self::assertSame($dateTime, $this->subject->getIncidentEscalationDate());
    }

    /**
     * @test
     */
    public function setProcessTableFieldReturnsInstanceOfItself(): void
    {
        $actual = $this->subject->setProcessTableField('some name', 'some value');

        self::assertSame($this->subject, $actual);
    }

    /**
     * @test
     */
    public function setAndGetProcessTableFieldAreCorrectImplementedForAStringValue(): void
    {
        $this->subject->setProcessTableField('some string', 'some value');

        self::assertSame('some value', $this->subject->getProcessTableField('some string'));
    }

    /**
     * @test
     */
    public function setAndGetProcessTableFieldAreCorrectImplementedForAnIntegerValue(): void
    {
        $this->subject->setProcessTableField('some integer', 42);

        self::assertSame(42, $this->subject->getProcessTableField('some integer'));
    }

    /**
     * @test
     */
    public function setAndGetProcessTableFieldAreCorrectImplementedForABooleanTrueValue(): void
    {
        $this->subject->setProcessTableField('some boolean', true);

        self::assertTrue($this->subject->getProcessTableField('some boolean'));
    }

    /**
     * @test
     */
    public function setAndGetProcessTableFieldAreCorrectImplementedForABooleanFalseValue(): void
    {
        $this->subject->setProcessTableField('some boolean', false);

        self::assertFalse($this->subject->getProcessTableField('some boolean'));
    }

    /**
     * @test
     */
    public function setAndGetProcessTableFieldAreImplementedCorrectlyForAFile(): void
    {
        $fileStub = $this->createStub(FileInterface::class);

        $actual = $this->subject->setProcessTableField('some name', $fileStub);

        self::assertSame($this->subject, $actual);
        self::assertSame($fileStub, $this->subject->getProcessTableField('some name'));
    }

    /**
     * @test
     */
    public function getProcessTableFieldsIsCorrectImplemented(): void
    {
        $this->subject
            ->setProcessTableField('some name', 'some value')
            ->setProcessTableField('some other name', 'some other value')
            ->setProcessTableField('some name', 'some overridden value');

        $expected = [
            'some name' => 'some overridden value',
            'some other name' => 'some other value',
        ];

        self::assertSame($expected, $this->subject->getProcessTableFields());
    }

    /**
     * @test
     */
    public function setAndGetRowsForSubTableIsCorrectImplemented(): void
    {
        $rows = [
            [
                'some subtable 1/1' => 'some value 1/1',
                'some subtable 1/2' => 'some value 1/2',
            ],
            [
                'some subtable 2/1' => 'some value 2/1',
                'some subtable 2/2' => 'some value 2/2',
            ],
        ];

        $actual = $this->subject->setRowsForSubTable('some subtable', $rows);

        self::assertSame($this->subject, $actual);
        self::assertSame($rows, $this->subject->getRowsForSubTable('some subtable'));
    }

    /**
     * @test
     */
    public function addRowToSubTableIsCorrectImplemented(): void
    {
        $row = [
            'some subtable 1' => 'some value 1',
            'some subtable 2' => 'some value 2',
        ];

        $actual = $this->subject->addRowToSubTable('some subtable', $row);

        self::assertSame($this->subject, $actual);
        self::assertSame([$row], $this->subject->getRowsForSubTable('some subtable'));
    }

    /**
     * @test
     */
    public function appendingRowToSubTable(): void
    {
        $rows = [
            [
                'some subtable 1/1' => 'some value 1/1',
                'some subtable 1/2' => 'some value 1/2',
            ],
        ];

        $this->subject->setRowsForSubTable('some subtable', $rows);

        $row = [
            'some subtable 2/1' => 'some value 2/1',
            'some subtable 2/2' => 'some value 2/2',
        ];

        $this->subject->addRowToSubTable('some subtable', $row);

        $expected = [
            ...$rows,
            $row,
        ];

        self::assertSame($expected, $this->subject->getRowsForSubTable('some subtable'));
    }
}
