<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Tests\Unit\Client;

use JobRouter\AddOn\RestClient\Client\ClientInterface;
use JobRouter\AddOn\RestClient\Client\IncidentsClientDecorator;
use JobRouter\AddOn\RestClient\Enumerations\Priority;
use JobRouter\AddOn\RestClient\Model\Incident;
use JobRouter\AddOn\RestClient\Resource\FileInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class IncidentsClientDecoratorTest extends TestCase
{
    private ClientInterface&MockObject $clientMock;
    private IncidentsClientDecorator $subject;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);

        $this->subject = new IncidentsClientDecorator($this->clientMock);
    }

    #[Test]
    public function requestIsPassedUnchangedToClientIfArrayIsGivenAsDataAndReturnsInstanceOfResponseInterface(): void
    {
        $responseStub = $this->createStub(ResponseInterface::class);

        $this->clientMock
            ->expects(self::once())
            ->method('request')
            ->with('GET', 'some/route', [
                'some' => 'data',
            ])
            ->willReturn($responseStub);

        $actual = $this->subject->request('GET', 'some/route', [
            'some' => 'data',
        ]);

        self::assertInstanceOf(ResponseInterface::class, $actual);
    }

    /**
     * @param array<string, mixed> $withMultipart
     */
    #[DataProvider('dataProvider')]
    #[Test]
    public function requestWithIncidentIsProcessedAsMultipartAndPassedToClient(
        Incident $incident,
        array $withMultipart,
    ): void {
        $this->clientMock
            ->expects(self::once())
            ->method('request')
            ->with('POST', 'some/route', self::identicalTo($withMultipart));

        $this->subject->request('POST', 'some/route', $incident);
    }

    public static function dataProvider(): iterable
    {
        yield 'Given step' => [
            new Incident(42),
            [
                'step' => '42',
            ],
        ];

        yield 'Given initiator' => [
            (new Incident(1))->setInitiator('some initiator'),
            [
                'step' => '1',
                'initiator' => 'some initiator',
            ],
        ];

        yield 'Given username' => [
            (new Incident(1))->setUsername('some username'),
            [
                'step' => '1',
                'username' => 'some username',
            ],
        ];

        yield 'Given jobfunction' => [
            (new Incident(1))->setJobFunction('some jobfunction'),
            [
                'step' => '1',
                'jobfunction' => 'some jobfunction',
            ],
        ];

        yield 'Given summary' => [
            (new Incident(1))->setSummary('some summary'),
            [
                'step' => '1',
                'summary' => 'some summary',
            ],
        ];

        yield 'Given priority' => [
            (new Incident(1))->setPriority(Priority::High),
            [
                'step' => '1',
                'priority' => '3',
            ],
        ];

        yield 'Given pool' => [
            (new Incident(1))->setPool(42),
            [
                'step' => '1',
                'pool' => '42',
            ],
        ];

        yield 'Given simulation is true' => [
            (new Incident(1))->setSimulation(true),
            [
                'step' => '1',
                'simulation' => '1',
            ],
        ];

        yield 'Given simulation is false' => [
            (new Incident(1))->setSimulation(false),
            [
                'step' => '1',
            ],
        ];

        yield 'Given step escalation date' => [
            (new Incident(1))->setStepEscalationDate(
                new \DateTimeImmutable(
                    '2020-01-30 12:34:56',
                    new \DateTimeZone('America/Chicago'),
                ),
            ),
            [
                'step' => '1',
                'step_escalation_date' => '2020-01-30T12:34:56-06:00',
            ],
        ];

        yield 'Given incident escalation date' => [
            (new Incident(1))->setIncidentEscalationDate(
                new \DateTimeImmutable(
                    '2020-01-31 01:23:45',
                    new \DateTimeZone('Europe/Berlin'),
                ),
            ),
            [
                'step' => '1',
                'incident_escalation_date' => '2020-01-31T01:23:45+01:00',
            ],
        ];

        $fileStub = new class() implements FileInterface {
            public function toArray(): array
            {
                return [];
            }
        };
        yield 'Given process table fields' => [
            (new Incident(1))
                ->setProcessTableField('some field', 'some value')
                ->setProcessTableField('another field', 'another value')
                ->setProcessTableField('different field', 'different value')
                ->setProcessTableField('integer field', 123)
                ->setProcessTableField('boolean true field', true)
                ->setProcessTableField('boolean false field', false)
                ->setProcessTableField('file field', $fileStub),
            [
                'step' => '1',
                'processtable[fields][0][name]' => 'some field',
                'processtable[fields][0][value]' => 'some value',
                'processtable[fields][1][name]' => 'another field',
                'processtable[fields][1][value]' => 'another value',
                'processtable[fields][2][name]' => 'different field',
                'processtable[fields][2][value]' => 'different value',
                'processtable[fields][3][name]' => 'integer field',
                'processtable[fields][3][value]' => '123',
                'processtable[fields][4][name]' => 'boolean true field',
                'processtable[fields][4][value]' => '1',
                'processtable[fields][5][name]' => 'boolean false field',
                'processtable[fields][5][value]' => '0',
                'processtable[fields][6][name]' => 'file field',
                'processtable[fields][6][value]' => $fileStub,
            ],
        ];

        yield 'Given sub table fields' => [
            (new Incident(1))
                ->setRowsForSubTable(
                    'some subtable',
                    [
                        [
                            'some string' => 'some value 1',
                            'some integer' => 123,
                            'some boolean' => true,
                        ],
                        [
                            'some string' => 'some value 2',
                            'some integer' => 234,
                            'some boolean' => false,
                        ],
                    ],
                )
                ->setRowsForSubTable(
                    'other subtable',
                    [
                        [
                            'other name 1/1' => 'other value 1/1',
                            'other name 1/2' => 'other value 1/2',
                        ],
                    ],
                ),
            [
                'step' => '1',
                'subtables[0][name]' => 'some subtable',
                'subtables[0][rows][0][fields][0][name]' => 'some string',
                'subtables[0][rows][0][fields][0][value]' => 'some value 1',
                'subtables[0][rows][0][fields][1][name]' => 'some integer',
                'subtables[0][rows][0][fields][1][value]' => '123',
                'subtables[0][rows][0][fields][2][name]' => 'some boolean',
                'subtables[0][rows][0][fields][2][value]' => '1',
                'subtables[0][rows][1][fields][0][name]' => 'some string',
                'subtables[0][rows][1][fields][0][value]' => 'some value 2',
                'subtables[0][rows][1][fields][1][name]' => 'some integer',
                'subtables[0][rows][1][fields][1][value]' => '234',
                'subtables[0][rows][1][fields][2][name]' => 'some boolean',
                'subtables[0][rows][1][fields][2][value]' => '0',
                'subtables[1][name]' => 'other subtable',
                'subtables[1][rows][0][fields][0][name]' => 'other name 1/1',
                'subtables[1][rows][0][fields][0][value]' => 'other value 1/1',
                'subtables[1][rows][0][fields][1][name]' => 'other name 1/2',
                'subtables[1][rows][0][fields][1][value]' => 'other value 1/2',
            ],
        ];
    }

    #[Test]
    public function requestWithIncidentIsProcessedAndReturnsInstanceOfResponseInterface(): void
    {
        $responseStub = $this->createStub(ResponseInterface::class);

        $this->clientMock
            ->expects(self::once())
            ->method('request')
            ->willReturn($responseStub);

        $actual = $this->subject->request('GET', 'some/route', new Incident(1));

        self::assertInstanceOf(ResponseInterface::class, $actual);
    }
}
