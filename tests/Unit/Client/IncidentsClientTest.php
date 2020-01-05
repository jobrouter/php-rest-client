<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Tests\Unit\Client;

use Brotkrueml\JobRouterClient\Client\IncidentsClient;
use Brotkrueml\JobRouterClient\Client\RestClient;
use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
use Brotkrueml\JobRouterClient\Model\Incident;
use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class IncidentsClientTest extends TestCase
{
    private const TEST_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqYXQiOjE1NzAyMjAwNzIsImp0aSI6IjhWMGtaSzJ5RzRxdGlhbjdGbGZTNUhPTGZaeGtZXC9obG1SVEV2VXIxVmwwPSIsImlzcyI6IkpvYlJvdXRlciIsIm5iZiI6MTU3MDIyMDA3MiwiZXhwIjoxNTcwMjIwMTAyLCJkYXRhIjp7InVzZXJuYW1lIjoicmVzdCJ9fQ.cbAyj36f9MhAwOMzlTEheRkHhuuIEOeb1Uy8i0KfUhU';

    /** @var ClientConfiguration */
    private static $configuration;

    /** @var MockWebServer */
    private static $server;

    /** @var \org\bovigo\vfs\vfsStreamDirectory */
    private $root;

    /** @var IncidentsClient */
    private $subject;

    public static function setUpBeforeClass(): void
    {
        self::$server = new MockWebServer;
        self::$server->start();

        self::$configuration = new ClientConfiguration(
            self::$server->getServerRoot(),
            'fake_username',
            'fake_password'
        );
    }

    public static function tearDownAfterClass(): void
    {
        self::$server->stop();
    }

    protected function setUp(): void
    {
        $this->root = vfsStream::setup();

        $this->setResponseOfTokensPath();
        $restClient = new RestClient(self::$configuration);
        $this->subject = new IncidentsClient($restClient);
    }

    private function setResponseOfTokensPath(): void
    {
        self::$server->setResponseOfPath(
            '/api/rest/v2/application/tokens',
            new Response(
                \sprintf('{"tokens":["%s"]}', self::TEST_TOKEN),
                ['content-type' => 'application/json'],
                201
            )
        );
    }

    /**
     * @test
     */
    public function authenticateIsPassedToRestClient(): void
    {
        // Send another request in between ...
        self::$server->setResponseOfPath(
            '/api/rest/v2/some/route',
            new Response('The response of some/route')
        );

        $this->subject->request('GET', 'some/route');

        $this->subject->authenticate();

        // ... to get really the last request uri
        $lastRequestUri = self::$server->getLastRequest()->getRequestUri();

        self::assertSame('/api/rest/v2/application/tokens', $lastRequestUri);
    }

    /**
     * @test
     */
    public function requestIsPassedToRestClientWhenNoIncidentIsGiven(): void
    {
        self::$server->setResponseOfPath(
            '/api/rest/v2/some/route',
            new Response('The response of some/route')
        );

        $response = $this->subject->request('GET', 'some/route');

        $responseContent = $response->getBody()->getContents();
        self::assertSame('The response of some/route', $responseContent);
    }

    /**
     * @test
     */
    public function requestWithIncidentIsProcessedCorrectly(): void
    {
        self::$server->setResponseOfPath(
            '/api/rest/v2/some/route',
            new Response('The response of some/route')
        );

        $incident = (new Incident())
            ->setStep(1)
            ->setInitiator('some initiator')
            ->setUsername('some username')
            ->setJobfunction('some jobfunction')
            ->setSummary('some summary')
            ->setPriority(Incident::PRIORITY_LOW)
            ->setPool(42)
            ->setSimulation(true)
            ->setStepEscalationDate(new \DateTime('2020-01-05T13:45:17+01:00'))
            ->setIncidentEscalationDate(new \DateTime('2020-01-05T13:46:43+01:00'))
            ->setProcessTableField('some process field name', 'some process field value')
            ->setProcessTableField('some other process field name', 'some other process field value')
            ->setRowsForSubTable(
                'some subtable',
                [
                    [
                        'some name 1/1' => 'some value 1/1',
                        'some name 1/2' => 'some value 1/2',
                    ],
                    [
                        'some name 2/1' => 'some value 2/1',
                        'some name 2/2' => 'some value 2/2',
                    ],
                ]
            )
            ->setRowsForSubTable(
                'other subtable',
                [
                    [
                        'other name 1/1' => 'other value 1/1',
                        'other name 1/2' => 'other value 1/2',
                    ],
                ]
            )
            ->addRowToSubTable(
                'some subtable',
                [
                    'some name 3/1' => 'some value 3/1',
                    'some name 3/2' => 'some value 3/2',
                ]
            );

        $this->subject->request('POST', 'some/route', $incident);

        $requestInput = self::$server->getLastRequest()->getParsedInput();

        $expected = [
            'step' => '1',
            'initiator' => 'some initiator',
            'username' => 'some username',
            'jobfunction' => 'some jobfunction',
            'summary' => 'some summary',
            'priority' => '1',
            'pool' => '42',
            'simulation' => '1',
            'step_escalation_date' => '2020-01-05T13:45:17+01:00',
            'incident_escalation_date' => '2020-01-05T13:46:43+01:00',
            'processtable' => [
                'fields' => [
                    0 => [
                        'name' => 'some process field name',
                        'value' => 'some process field value',
                    ],
                    1 => [
                        'name' => 'some other process field name',
                        'value' => 'some other process field value',
                    ],
                ],
            ],
            'subtables' => [
                0 => [
                    'name' => 'some subtable',
                    'rows' => [
                        0 => [
                            'fields' => [
                                0 => [
                                    'name' => 'some name 1/1',
                                    'value' => 'some value 1/1',
                                ],
                                1 => [
                                    'name' => 'some name 1/2',
                                    'value' => 'some value 1/2',
                                ],
                            ],
                        ],
                        1 => [
                            'fields' => [
                                0 => [
                                    'name' => 'some name 2/1',
                                    'value' => 'some value 2/1',
                                ],
                                1 => [
                                    'name' => 'some name 2/2',
                                    'value' => 'some value 2/2',
                                ],
                            ],
                        ],
                        2 => [
                            'fields' => [
                                0 => [
                                    'name' => 'some name 3/1',
                                    'value' => 'some value 3/1',
                                ],
                                1 => [
                                    'name' => 'some name 3/2',
                                    'value' => 'some value 3/2',
                                ],
                            ],
                        ],
                    ],
                ],
                1 => [
                    'name' => 'other subtable',
                    'rows' => [
                        0 => [
                            'fields' => [
                                0 => [
                                    'name' => 'other name 1/1',
                                    'value' => 'other value 1/1',
                                ],
                                1 => [
                                    'name' => 'other name 1/2',
                                    'value' => 'other value 1/2',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        self::assertSame($expected, $requestInput);
    }
}
