<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Tests\Unit\Client;

use Brotkrueml\JobRouterClient\Client\RestClient;
use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
use Brotkrueml\JobRouterClient\Exception\AuthenticationException;
use Brotkrueml\JobRouterClient\Exception\HttpException;
use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use PHPUnit\Framework\TestCase;

class RestClientTest extends TestCase
{
    private const TEST_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqYXQiOjE1NzAyMjAwNzIsImp0aSI6IjhWMGtaSzJ5RzRxdGlhbjdGbGZTNUhPTGZaeGtZXC9obG1SVEV2VXIxVmwwPSIsImlzcyI6IkpvYlJvdXRlciIsIm5iZiI6MTU3MDIyMDA3MiwiZXhwIjoxNTcwMjIwMTAyLCJkYXRhIjp7InVzZXJuYW1lIjoicmVzdCJ9fQ.cbAyj36f9MhAwOMzlTEheRkHhuuIEOeb1Uy8i0KfUhU';

    /** @var ClientConfiguration */
    private static $configuration;

    /** @var MockWebServer */
    private static $server;

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
    public function requestWithHeaderOptionsIsCalledWithThem(): void
    {
        $this->setResponseOfTokensPath();

        $restClient = new RestClient(self::$configuration);

        self::$server->setResponseOfPath(
            '/api/rest/v2/some/route',
            new Response('The response of some/route')
        );

        $options = [
            'headers' => [
                'some-test-header' => 'some-test-value',
            ],
        ];

        $restClient->request('GET', 'some/route', $options);

        $requestHeaders = self::$server->getLastRequest()->getHeaders();

        self::assertArrayHasKey('some-test-header', $requestHeaders);
        self::assertSame('some-test-value', $requestHeaders['some-test-header']);
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
    public function unknownOptionPassingToRequestThrowsRestClientException(): void
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
    public function noTokenIsReturnedThrowsRestClientException(): void
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

        self::$configuration->setUserAgentAddition('AdditionToUserAgent');
        $restClient = new RestClient(self::$configuration);

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
                    'Authentication failed. Please provide valid credentials and check if the user is not blocked.'
                ),
                ['content-type' => 'application/json'],
                401
            )
        );

        new RestClient(self::$configuration);
    }
}
