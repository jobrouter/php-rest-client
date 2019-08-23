<?php

namespace Brotkrueml\JobRouterClient\Tests\Unit\Client;

use Brotkrueml\JobRouterClient\Client\RestClient;
use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
use Brotkrueml\JobRouterClient\Exception\RestException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RestClientTest extends TestCase
{
    /** @var ClientConfiguration */
    private $configuration;

    public function setUp(): void
    {
        $this->configuration = new ClientConfiguration(
            'http://example.org/jobrouter/',
            'fake_username',
            'fake_password'
        );
    }

    /**
     * @test
     */
    public function httpClientIsCreatedUponInitialization(): void
    {
        $restClientMock = new class($this->configuration) extends RestClient {
            public function authenticate(): void
            {
            }

            public function getClient()
            {
                return $this->client;
            }
        };

        $actual = $restClientMock->getClient();

        $this->assertInstanceOf(HttpClientInterface::class, $actual);
    }

    /**
     * @test
     */
    public function authenticateIsCalledUponInitialisationOfRestClientAndStoresTheTokenInternally(): void
    {
        $usedToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.ey...';

        $responseBody = json_encode(
            [
                'tokens' => [
                    $usedToken,
                ],
            ]
        );

        $responses = [
            new MockResponse($responseBody),
        ];

        $httpClient = new MockHttpClient($responses, 'http://example.net/jobrouter/rest/api/v2/');

        $reflector = new \ReflectionClass(RestClient::class);

        /** @noinspection PhpUnhandledExceptionInspection */
        $jwToken = $reflector->getProperty('jwToken');
        $jwToken->setAccessible(true);

        $subject = new RestClient($this->configuration, $httpClient);

        $this->assertSame($usedToken, $jwToken->getValue($subject));
    }

    /**
     * @test
     */
    public function authenticateThrowsExceptionOnErroneousResponse(): void
    {
        $this->expectException(RestException::class);

        $responses = [
            new MockResponse('', ['error' => 'Some error occurred!']),
        ];

        $httpClient = new MockHttpClient($responses, 'http://example.net/jobrouter/rest/api/v2/');

        new RestClient($this->configuration, $httpClient);
    }

    /**
     * @test
     */
    public function requestGivesResponseBack(): void
    {
        $authenticateBody = json_encode(
            [
                'tokens' => [
                    'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.ey...',
                ],
            ]
        );

        $callback = function ($method, $url, $options) use ($authenticateBody) {
            if ($url === 'http://example.net/jobrouter/rest/api/v2/application/tokens') {
                return new MockResponse($authenticateBody);
            }

            if ($url === 'http://example.net/jobrouter/rest/api/v2/some/route') {
                return new MockResponse('The response of the request');
            }
        };

        $httpClient = new MockHttpClient($callback, 'http://example.net/jobrouter/rest/api/v2/');

        $subject = new RestClient($this->configuration, $httpClient);
        $actual = $subject->request('some/route');

        $this->assertSame('The response of the request', $actual->getContent());
    }

    /**
     * @test
     */
    public function requestThrowsRestExceptionOnError(): void
    {
        $this->expectException(RestException::class);

        $authenticateBody = json_encode(
            [
                'tokens' => [
                    'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.ey...',
                ],
            ]
        );

        $callback = function ($method, $url, $options) use ($authenticateBody) {
            if ($url === 'http://example.net/jobrouter/rest/api/v2/application/tokens') {
                return new MockResponse($authenticateBody);
            }

            if ($url === 'http://example.net/jobrouter/rest/api/v2/some/route') {
                throw new TransportException();
            }
        };

        $httpClient = new MockHttpClient($callback, 'http://example.net/jobrouter/rest/api/v2/');

        $subject = new RestClient($this->configuration, $httpClient);
        $subject->request('some/route');
    }
}
