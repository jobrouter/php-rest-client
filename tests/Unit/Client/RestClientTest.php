<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Tests\Unit\Client;

use Brotkrueml\JobRouterClient\Client\RestClient;
use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
use Brotkrueml\JobRouterClient\Exception\AuthenticationException;
use Brotkrueml\JobRouterClient\Exception\HttpException;
use Brotkrueml\JobRouterClient\Exception\RestClientException;
use Brotkrueml\JobRouterClient\Resource\FileInterface;
use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class RestClientTest extends TestCase
{
    private const TEST_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqYXQiOjE1NzAyMjAwNzIsImp0aSI6IjhWMGtaSzJ5RzRxdGlhbjdGbGZTNUhPTGZaeGtZXC9obG1SVEV2VXIxVmwwPSIsImlzcyI6IkpvYlJvdXRlciIsIm5iZiI6MTU3MDIyMDA3MiwiZXhwIjoxNTcwMjIwMTAyLCJkYXRhIjp7InVzZXJuYW1lIjoicmVzdCJ9fQ.cbAyj36f9MhAwOMzlTEheRkHhuuIEOeb1Uy8i0KfUhU';

    /** @var ClientConfiguration */
    private static $configuration;

    /** @var MockWebServer */
    private static $server;

    /** @var \org\bovigo\vfs\vfsStreamDirectory */
    private $root;

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
    }

    /**
     * @test
     */
    public function testTokensRouteIsCorrectlyCalled(): void
    {
        $this->setResponseOfTokensPath();

        $restClient = new RestClient(self::$configuration);

        self::assertInstanceOf(RestClient::class, $restClient);
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
    public function wrongTokensRouteThrowsException(): void
    {
        $this->expectException(AuthenticationException::class);

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/tokens',
            new Response('not found', [], 404)
        );

        new RestClient(self::$configuration);
    }

    /**
     * @test
     */
    public function requestIsCalledCorrectly(): void
    {
        $this->setResponseOfTokensPath();

        $restClient = new RestClient(self::$configuration);

        self::$server->setResponseOfPath(
            '/api/rest/v2/some/route',
            new Response('The response of some/route')
        );

        $response = $restClient->request('GET', 'some/route');

        $responseContent = $response->getBody()->getContents();
        $requestHeaders = self::$server->getLastRequest()->getHeaders();

        self::assertSame('The response of some/route', $responseContent);
        self::assertArrayHasKey('X-Jobrouter-Authorization', $requestHeaders);
        self::assertSame('Bearer ' . self::TEST_TOKEN, $requestHeaders['X-Jobrouter-Authorization']);
    }

    /**
     * @test
     */
    public function serverIsNotAvailable(): void
    {
        $this->expectException(AuthenticationException::class);

        $configuration = new ClientConfiguration(
            'http://' . self::$server->getHost() . ':' . (self::$server->getPort() - 1) . '/',
            'fake_username',
            'fake_password'
        );

        new RestClient($configuration);
    }

    /**
     * @test
     */
    public function unknownOptionPassingToRequestThrowsException(): void
    {
        $this->expectException(HttpException::class);

        $this->setResponseOfTokensPath();

        $restClient = new RestClient(self::$configuration);

        self::$server->setResponseOfPath(
            '/api/rest/v2/some/route',
            new Response('The response of some/route')
        );

        $restClient->request('some/route', 'GET', ['unknown_option' => '']);
    }

    /**
     * @test
     */
    public function noTokenIsReturnedThrowsAuthenticationException(): void
    {
        $this->expectException(AuthenticationException::class);

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/tokens',
            new Response(
                '{}',
                ['content-type' => 'application/json'],
                201
            )
        );

        new RestClient(self::$configuration);
    }

    /**
     * @test
     */
    public function defaultUserAgentIsSendCorrectly(): void
    {
        $this->setResponseOfTokensPath();

        $restClient = new RestClient(self::$configuration);

        self::$server->setResponseOfPath(
            '/api/rest/v2/some/route',
            new Response('The response of some/route')
        );

        $restClient->request('GET', 'some/route');
        $requestHeaders = self::$server->getLastRequest()->getHeaders();

        self::assertArrayHasKey('User-Agent', $requestHeaders);
        self::assertStringStartsWith('JobRouterClient/', $requestHeaders['User-Agent']);
        self::assertStringEndsWith(' (https://github.com/brotkrueml/jobrouter-client)', $requestHeaders['User-Agent']);
    }

    /**
     * @test
     */
    public function appendedUserAgentIsSendCorrectly(): void
    {
        $this->setResponseOfTokensPath();

        $configuration = self::$configuration->withUserAgentAddition('AdditionToUserAgent');
        $restClient = new RestClient($configuration);

        self::$server->setResponseOfPath(
            '/api/rest/v2/some/route',
            new Response('The response of some/route')
        );

        $restClient->request('GET', 'some/route');
        $requestHeaders = self::$server->getLastRequest()->getHeaders();

        self::assertArrayHasKey('User-Agent', $requestHeaders);
        self::assertStringStartsWith('JobRouterClient/', $requestHeaders['User-Agent']);
        self::assertStringEndsWith(') AdditionToUserAgent', $requestHeaders['User-Agent']);
    }

    /**
     * @test
     */
    public function errorMessageIsCorrectGivenWhenAuthenticationError(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessageMatches('/^Authentication failed for user "fake_username" on JobRouter base URL "/');

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/tokens',
            new Response(
                \sprintf(
                    '{"errors":["-": ["%s"]]}',
                    'Some bad request'
                ),
                ['content-type' => 'application/json'],
                400
            )
        );

        new RestClient(self::$configuration);
    }

    /**
     * @test
     */
    public function errorMessageIsCorrectGivenWhenRequestError(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('Error fetching route some/route: Some error occured.');

        $this->setResponseOfTokensPath();

        self::$server->setResponseOfPath(
            '/api/rest/v2/some/route',
            new Response(
                \sprintf(
                    '{"errors":{"-": ["%s"]}}',
                    'Some error occured.'
                ),
                ['content-type' => 'application/json'],
                404
            )
        );

        (new RestClient(self::$configuration))->request('GET', 'some/route');
    }

    /**
     * @test
     */
    public function formDataIsCorrectlySend(): void
    {
        $this->setResponseOfTokensPath();

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/incidents/test',
            new Response(
                '{"incidents":[{"workflowId":"8c520dd91b59c62c9ec30c7310bb9fc60000000313","stepId":"8c520dd91b59c62c9ec30c7310bb9fc60000000347","processId":"8c520dd91b59c62c9ec30c7310bb9fc60000000237","incidentnumber":17,"jobfunction":"Admin","username":"rest"}]}',
                ['content-type' => 'application/json'],
                200
            )
        );

        $restClient = new RestClient(self::$configuration);

        $filePath = $this->root->url() . '/some-file.txt';
        \file_put_contents($filePath, 'foo');

        $formData = [
            'step' => '1',
            'summary' => 'RestClientTest',
            'processtable[fields][0][name]' => 'textbox',
            'processtable[fields][0][value]' => 'value for a textbox',
            'processtable[fields][1][name]' => 'file',
            'processtable[fields][1][value]' => [
                'path' => $filePath,
                'filename' => 'bar.txt',
            ],
        ];

        $response = $restClient->request(
            'POST',
            'application/incidents/test',
            $formData
        );

        self::assertSame(200, $response->getStatusCode());

        $lastRequest = self::$server->getLastRequest();

        $requestHeaders = $lastRequest->getHeaders();
        self::assertArrayHasKey('X-Jobrouter-Authorization', $requestHeaders);
        self::assertSame('Bearer ' . self::TEST_TOKEN, $requestHeaders['X-Jobrouter-Authorization']);
        self::assertArrayHasKey('Content-Type', $requestHeaders);
        self::assertStringStartsWith('multipart/form-data; boundary="', $requestHeaders['Content-Type']);

        $post = $lastRequest->getPost();
        self::assertArrayHasKey('step', $post);
        self::assertSame('1', $post['step']);
        self::assertArrayHasKey('summary', $post);
        self::assertSame('RestClientTest', $post['summary']);
        self::assertArrayHasKey('processtable', $post);
        self::assertSame('textbox', $post['processtable']['fields'][0]['name']);
        self::assertSame('value for a textbox', $post['processtable']['fields'][0]['value']);
        self::assertSame('file', $post['processtable']['fields'][1]['name']);

        $files = $lastRequest->getFiles();
        self::assertArrayHasKey('processtable', $files);
        self::assertSame('bar.txt', $files['processtable']['name']['fields'][1]['value']);
    }

    /**
     * @test
     */
    public function requestWhenDataIsNotAnArrayAnExceptionIsThrown(): void
    {
        $this->expectException(RestClientException::class);
        $this->expectExceptionCode(1578233543);

        $this->setResponseOfTokensPath();
        $restClient = new RestClient(self::$configuration);

        $restClient->request('POST', 'some/route', false);
    }

    /**
     * @test
     */
    public function requestUsingFileInterfaceIsHandledCorrectly(): void
    {
        $this->setResponseOfTokensPath();

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/incidents/test',
            new Response(
                '{"incidents":[{"workflowId":"8c520dd91b59c62c9ec30c7310bb9fc60000000313","stepId":"8c520dd91b59c62c9ec30c7310bb9fc60000000347","processId":"8c520dd91b59c62c9ec30c7310bb9fc60000000237","incidentnumber":17,"jobfunction":"Admin","username":"rest"}]}',
                ['content-type' => 'application/json'],
                200
            )
        );

        $restClient = new RestClient(self::$configuration);

        $filePath = $this->root->url() . '/some-file.txt';
        \file_put_contents($filePath, 'foo');

        $fileMock = $this->createMock(FileInterface::class);

        $fileMock
            ->expects(self::once())
            ->method('toArray')
            ->willReturn([
                'path' => $filePath,
                'filename' => 'foo.txt',
            ]);

        $formData = [
            'step' => '1',
            'processtable[fields][0][name]' => 'file',
            'processtable[fields][0][value]' => $fileMock,
        ];

        $response = $restClient->request(
            'POST',
            'application/incidents/test',
            $formData
        );

        self::assertSame(200, $response->getStatusCode());

        $lastRequest = self::$server->getLastRequest();

        $post = $lastRequest->getPost();
        self::assertArrayHasKey('processtable', $post);
        self::assertSame('file', $post['processtable']['fields'][0]['name']);

        $files = $lastRequest->getFiles();
        self::assertArrayHasKey('processtable', $files);
        self::assertSame('foo.txt', $files['processtable']['name']['fields'][0]['value']);
    }
}
