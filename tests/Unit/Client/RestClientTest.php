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

use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use JobRouter\AddOn\RestClient\Client\RestClient;
use JobRouter\AddOn\RestClient\Configuration\ClientConfiguration;
use JobRouter\AddOn\RestClient\Configuration\ClientOptions;
use JobRouter\AddOn\RestClient\Exception\AuthenticationException;
use JobRouter\AddOn\RestClient\Exception\HttpException;
use JobRouter\AddOn\RestClient\Exception\RestClientException;
use JobRouter\AddOn\RestClient\Resource\FileInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RestClientTest extends TestCase
{
    private const TEST_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqYXQiOjE1NzAyMjAwNzIsImp0aSI6IjhWMGtaSzJ5RzRxdGlhbjdGbGZTNUhPTGZaeGtZXC9obG1SVEV2VXIxVmwwPSIsImlzcyI6IkpvYlJvdXRlciIsIm5iZiI6MTU3MDIyMDA3MiwiZXhwIjoxNTcwMjIwMTAyLCJkYXRhIjp7InVzZXJuYW1lIjoicmVzdCJ9fQ.cbAyj36f9MhAwOMzlTEheRkHhuuIEOeb1Uy8i0KfUhU';

    private static ClientConfiguration $configuration;
    private static MockWebServer $server;

    public static function setUpBeforeClass(): void
    {
        self::$server = new MockWebServer();
        self::$server->start();

        self::$configuration = new ClientConfiguration(
            self::$server->getServerRoot() . '/',
            'fake_username',
            'fake_password',
        );
    }

    public static function tearDownAfterClass(): void
    {
        self::$server->stop();
    }

    #[Test]
    public function tokensRouteIsCorrectlyCalled(): void
    {
        $this->setResponseOfTokensPath();

        $restClient = new RestClient(self::$configuration);
        $restClient->authenticate();

        self::assertInstanceOf(RestClient::class, $restClient);

        $input = \json_decode(self::$server->getLastRequest()->getInput(), true, flags: \JSON_THROW_ON_ERROR);

        self::assertSame('fake_username', $input['username']);
        self::assertSame('fake_password', $input['password']);
        self::assertSame(600, $input['lifetime']);
    }

    private function setResponseOfTokensPath(): void
    {
        self::$server->setResponseOfPath(
            '/api/rest/v2/application/tokens',
            new Response(
                \sprintf('{"tokens":["%s"]}', self::TEST_TOKEN),
                [
                    'content-type' => 'application/json',
                ],
                201,
            ),
        );
    }

    #[Test]
    public function wrongTokensRouteThrowsHttpException(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage(\sprintf(
            'Error fetching resource "http://127.0.0.1:%d/api/rest/v2/application/tokens": not found',
            self::$server->getPort(),
        ));

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/tokens',
            new Response('not found', [], 404),
        );

        (new RestClient(self::$configuration))->authenticate();
    }

    #[Test]
    public function whenAuthenticationFailsAuthenticationExceptionIsThrown(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionCode(1577818398);
        $this->expectExceptionMessage(\sprintf(
            'Authentication failed for user "fake_username" on JobRouter base URL "http://127.0.0.1:%d/"',
            self::$server->getPort(),
        ));

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/tokens',
            new Response('invalid', [], 401),
        );

        (new RestClient(self::$configuration))->authenticate();
    }

    #[Test]
    public function whenAuthenticationResponseIsMalformedAuthenticationExceptionIsThrown(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionCode(1612552955);
        $this->expectExceptionMessage('Authorisation response is malformed');

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/tokens',
            new Response('invalid', [], 200),
        );

        (new RestClient(self::$configuration))->authenticate();
    }

    #[Test]
    public function authenticateReturnsObjectToRestClient(): void
    {
        $this->setResponseOfTokensPath();
        $restClient = new RestClient(self::$configuration);

        $actual = $restClient->authenticate();

        self::assertSame($restClient, $actual);
    }

    #[Test]
    public function requestIsCalledCorrectly(): void
    {
        $this->setResponseOfTokensPath();

        $restClient = new RestClient(self::$configuration);
        $restClient->authenticate();

        self::$server->setResponseOfPath(
            '/api/rest/v2/some/route',
            new Response('The response of some/route'),
        );

        $response = $restClient->request('GET', '//some/route');

        $responseContent = $response->getBody()->getContents();
        $requestHeaders = self::$server->getLastRequest()?->getHeaders() ?? [];

        self::assertSame('The response of some/route', $responseContent);
        self::assertArrayHasKey('X-Jobrouter-Authorization', $requestHeaders);
        self::assertSame('Bearer ' . self::TEST_TOKEN, $requestHeaders['X-Jobrouter-Authorization']);
    }

    #[Test]
    public function startingSlashFromGivenResourceIsTrimmed(): void
    {
        $this->setResponseOfTokensPath();

        $restClient = new RestClient(self::$configuration);
        $restClient->authenticate();

        self::$server->setResponseOfPath(
            '/api/rest/v2/some/route',
            new Response('The response of some/route'),
        );

        $restClient->request('GET', '/some/route');
        $requestResource = self::$server->getLastRequest()?->getParsedUri()['path'] ?? '';

        self::assertSame('/api/rest/v2/some/route', $requestResource);
    }

    #[Test]
    public function jobRouterVersionBeforeAuthenticationIsEmptyString(): void
    {
        $restClient = new RestClient(self::$configuration);

        self::assertSame('', $restClient->getJobRouterVersion());
    }

    #[Test]
    public function jobRouterVersionIsDetected(): void
    {
        $version = '2022.4.0';

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/tokens',
            new Response(
                \sprintf('{"tokens":["%s"]}', self::TEST_TOKEN),
                [
                    'x-jobrouter-version' => $version,
                ],
                201,
            ),
        );

        $restClient = new RestClient(self::$configuration);
        $restClient->authenticate();

        self::assertSame($version, $restClient->getJobRouterVersion());
    }

    #[Test]
    public function redirectThrowsHttpException(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(307);
        $this->expectExceptionMessage(\sprintf(
            'Redirect "307" from "http://127.0.0.1:%d/api/rest/v2/application/tokens" to "https://example.org/redirect-destination.html" occurred',
            self::$server->getPort(),
        ));

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/tokens',
            new Response(
                '',
                [
                    'location' => 'https://example.org/redirect-destination.html',
                ],
                307,
            ),
        );

        (new RestClient(self::$configuration))->authenticate();
    }

    #[Test]
    public function serverIsNotAvailableThrowsHttpException(): void
    {
        $notConnectedPort = self::$server->getPort() - 1;

        $this->expectException(HttpException::class);
        $this->expectExceptionMessageMatches(\sprintf(
            '#Error fetching resource "http://127.0.0.1:%d/api/rest/v2/application/tokens": cURL error 7: Failed to connect to 127.0.0.1 port %d#',
            $notConnectedPort,
            $notConnectedPort,
        ));

        $configuration = new ClientConfiguration(
            'http://' . self::$server->getHost() . ':' . $notConnectedPort . '/',
            'fake_username',
            'fake_password',
        );

        (new RestClient($configuration))->authenticate();
    }

    #[Test]
    public function noTokenIsReturnedThrowsAuthenticationException(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionCode(1570222016);

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/tokens',
            new Response(
                '{}',
                [
                    'content-type' => 'application/json',
                ],
                201,
            ),
        );

        (new RestClient(self::$configuration))->authenticate();
    }

    #[Test]
    public function defaultUserAgentIsSendCorrectly(): void
    {
        $this->setResponseOfTokensPath();

        $restClient = new RestClient(self::$configuration);
        $restClient->authenticate();

        self::$server->setResponseOfPath(
            '/api/rest/v2/some/route',
            new Response('The response of some/route'),
        );

        $restClient->request('GET', 'some/route');
        $requestHeaders = self::$server?->getLastRequest()->getHeaders() ?? [];

        self::assertArrayHasKey('User-Agent', $requestHeaders);
        self::assertStringStartsWith('JobRouterClient/', $requestHeaders['User-Agent']);
        self::assertStringEndsWith(' (https://github.com/jobrouter/php-rest-client)', $requestHeaders['User-Agent']);
    }

    #[Test]
    public function appendedUserAgentIsSendCorrectly(): void
    {
        $this->setResponseOfTokensPath();

        $configuration = self::$configuration->withUserAgentAddition('AdditionToUserAgent');
        $restClient = new RestClient($configuration);
        $restClient->authenticate();

        self::$server->setResponseOfPath(
            '/api/rest/v2/some/route',
            new Response('The response of some/route'),
        );

        $restClient->request('GET', 'some/route');
        $requestHeaders = self::$server->getLastRequest()?->getHeaders() ?? [];

        self::assertArrayHasKey('User-Agent', $requestHeaders);
        self::assertStringStartsWith('JobRouterClient/', $requestHeaders['User-Agent']);
        self::assertStringEndsWith(') AdditionToUserAgent', $requestHeaders['User-Agent']);
    }

    #[Test]
    public function adjustedClientOptionsAreAssignedToClientCorrectly(): void
    {
        // We are testing a proxy server which should not respond, therefore an exception
        $this->expectException(HttpException::class);
        $this->expectExceptionMessageMatches('/Failed to connect to 127.0.0.1 port 9999/');

        $this->setResponseOfTokensPath();

        $clientOptions = new ClientOptions(
            proxy: 'http://127.0.0.1:9999/',
        );
        $configuration = self::$configuration->withClientOptions($clientOptions);
        $restClient = new RestClient($configuration);
        $restClient->authenticate();

        $restClient->request('GET', 'some/route');
    }

    #[Test]
    public function errorMessageIsCorrectGivenWhenStatusCodeIs300(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(\sprintf(
            'Redirect "300" from "http://127.0.0.1:%d/api/rest/v2/application/tokens" to "https://example.com/" occurred',
            self::$server->getPort(),
        ));

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/tokens',
            new Response('', ['Location: https://example.com/'], 300),
        );

        (new RestClient(self::$configuration))->authenticate();
    }

    #[Test]
    public function errorMessageIsCorrectGivenWhenStatusCodeIs400(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(\sprintf(
            'Error fetching resource "http://127.0.0.1:%d/api/rest/v2/application/tokens": {"errors":["-": ["Some bad request"]]}',
            self::$server->getPort(),
        ));

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/tokens',
            new Response(
                \sprintf(
                    '{"errors":["-": ["%s"]]}',
                    'Some bad request',
                ),
                [
                    'content-type' => 'application/json',
                ],
                400,
            ),
        );

        (new RestClient(self::$configuration))->authenticate();
    }

    #[Test]
    public function errorMessageIsCorrectGivenWhenRequestError(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage(\sprintf(
            'Error fetching resource "http://127.0.0.1:%d/api/rest/v2/some/route": {"errors":{"-": ["Some error occured."]}}',
            self::$server->getPort(),
        ));

        $this->setResponseOfTokensPath();

        self::$server->setResponseOfPath(
            '/api/rest/v2/some/route',
            new Response(
                \sprintf(
                    '{"errors":{"-": ["%s"]}}',
                    'Some error occured.',
                ),
                [
                    'content-type' => 'application/json',
                ],
                404,
            ),
        );

        $restClient = new RestClient(self::$configuration);
        $restClient->authenticate();
        $restClient->request('GET', 'some/route');
    }

    #[Test]
    public function formDataIsCorrectlySend(): void
    {
        $this->setResponseOfTokensPath();

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/incidents/test',
            new Response(
                '{"incidents":[{"workflowId":"8c520dd91b59c62c9ec30c7310bb9fc60000000313","stepId":"8c520dd91b59c62c9ec30c7310bb9fc60000000347","processId":"8c520dd91b59c62c9ec30c7310bb9fc60000000237","incidentnumber":17,"jobfunction":"Admin","username":"rest"}]}',
                [
                    'content-type' => 'application/json',
                ],
                200,
            ),
        );

        $restClient = new RestClient(self::$configuration);
        $restClient->authenticate();

        $filePath = \tempnam('/tmp', 'jrc_');
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
            $formData,
        );

        self::assertSame(200, $response->getStatusCode());

        $lastRequest = self::$server->getLastRequest();

        $requestHeaders = $lastRequest->getHeaders();
        self::assertArrayHasKey('X-Jobrouter-Authorization', $requestHeaders);
        self::assertSame('Bearer ' . self::TEST_TOKEN, $requestHeaders['X-Jobrouter-Authorization']);
        self::assertArrayHasKey('Content-Type', $requestHeaders);
        self::assertStringStartsWith('multipart/form-data; boundary=', $requestHeaders['Content-Type']);

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

        \unlink($filePath);
    }

    #[Test]
    public function requestWhenDataIsABooleanAnExceptionIsThrown(): void
    {
        $this->expectException(RestClientException::class);
        $this->expectExceptionCode(1578233543);
        $this->expectExceptionMessage('data must be an array, "bool" given');

        $this->setResponseOfTokensPath();
        $restClient = new RestClient(self::$configuration);
        $restClient->authenticate();

        $restClient->request('POST', 'some/route', false);
    }

    #[Test]
    public function requestWhenDataIsAClassAnExceptionIsThrown(): void
    {
        $this->expectException(RestClientException::class);
        $this->expectExceptionCode(1578233543);
        $this->expectExceptionMessage('data must be an array, "stdClass" given');

        $this->setResponseOfTokensPath();
        $restClient = new RestClient(self::$configuration);
        $restClient->authenticate();

        $restClient->request('POST', 'some/route', new \stdClass());
    }

    #[Test]
    public function requestUsingFileInterfaceIsHandledCorrectly(): void
    {
        $this->setResponseOfTokensPath();

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/incidents/test',
            new Response(
                '{"incidents":[{"workflowId":"8c520dd91b59c62c9ec30c7310bb9fc60000000313","stepId":"8c520dd91b59c62c9ec30c7310bb9fc60000000347","processId":"8c520dd91b59c62c9ec30c7310bb9fc60000000237","incidentnumber":17,"jobfunction":"Admin","username":"rest"}]}',
                [
                    'content-type' => 'application/json',
                ],
                200,
            ),
        );

        $restClient = new RestClient(self::$configuration);
        $restClient->authenticate();

        $filePath = \tempnam('/tmp', 'jrc_');
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
            $formData,
        );

        self::assertSame(200, $response->getStatusCode());

        $lastRequest = self::$server->getLastRequest();

        $post = $lastRequest->getPost();
        self::assertArrayHasKey('processtable', $post);
        self::assertSame('file', $post['processtable']['fields'][0]['name']);

        $files = $lastRequest->getFiles();
        self::assertArrayHasKey('processtable', $files);
        self::assertSame('foo.txt', $files['processtable']['name']['fields'][0]['value']);

        \unlink($filePath);
    }
}
