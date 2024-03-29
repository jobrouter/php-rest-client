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
use JobRouter\AddOn\RestClient\Client\ClientFactory;
use JobRouter\AddOn\RestClient\Client\DocumentsClientDecorator;
use JobRouter\AddOn\RestClient\Client\IncidentsClientDecorator;
use JobRouter\AddOn\RestClient\Client\RestClient;
use JobRouter\AddOn\RestClient\Configuration\ClientConfiguration;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ClientFactoryTest extends TestCase
{
    private const TEST_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqYXQiOjE1NzAyMjAwNzIsImp0aSI6IjhWMGtaSzJ5RzRxdGlhbjdGbGZTNUhPTGZaeGtZXC9obG1SVEV2VXIxVmwwPSIsImlzcyI6IkpvYlJvdXRlciIsIm5iZiI6MTU3MDIyMDA3MiwiZXhwIjoxNTcwMjIwMTAyLCJkYXRhIjp7InVzZXJuYW1lIjoicmVzdCJ9fQ.cbAyj36f9MhAwOMzlTEheRkHhuuIEOeb1Uy8i0KfUhU';

    private static MockWebServer $server;

    public static function setUpBeforeClass(): void
    {
        self::$server = new MockWebServer();
        self::$server->start();
    }

    public static function tearDownAfterClass(): void
    {
        self::$server->stop();
    }

    protected function setUp(): void
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
    public function clientFactoryCannotBeInstantiated(): void
    {
        $this->expectException(\Error::class);

        new ClientFactory();
    }

    #[Test]
    public function createRestClientReturnsAnInstanceOfRestClientWithDefaultLifetimeCorrectly(): void
    {
        $reflector = new \ReflectionClass(RestClient::class);
        $configurationProperty = $reflector->getProperty('configuration');
        $configurationProperty->setAccessible(true);

        $client = ClientFactory::createRestClient(
            self::$server->getServerRoot() . '/',
            'fake_username',
            'fake_password',
        );

        self::assertInstanceOf(RestClient::class, $client);
        self::assertSame(
            self::$server->getServerRoot() . '/',
            $configurationProperty->getValue($client)->getJobRouterSystem()->getBaseUrl(),
        );
        self::assertSame('fake_username', $configurationProperty->getValue($client)->getUsername());
        self::assertSame('fake_password', $configurationProperty->getValue($client)->getPassword());
        self::assertSame(
            ClientConfiguration::DEFAULT_TOKEN_LIFETIME_IN_SECONDS,
            $configurationProperty->getValue($client)->getLifetime(),
        );
    }

    #[Test]
    public function createRestClientReturnsAnInstanceOfRestClientWithAdjustedLifetimeCorrectly(): void
    {
        $reflector = new \ReflectionClass(RestClient::class);
        $configurationProperty = $reflector->getProperty('configuration');
        $configurationProperty->setAccessible(true);

        $client = ClientFactory::createRestClient(
            self::$server->getServerRoot() . '/',
            'fake_username',
            'fake_password',
            42,
        );

        self::assertInstanceOf(RestClient::class, $client);
        self::assertSame(
            self::$server->getServerRoot() . '/',
            $configurationProperty->getValue($client)->getJobRouterSystem()->getBaseUrl(),
        );
        self::assertSame('fake_username', $configurationProperty->getValue($client)->getUsername());
        self::assertSame('fake_password', $configurationProperty->getValue($client)->getPassword());
        self::assertSame(42, $configurationProperty->getValue($client)->getLifetime());
    }

    #[Test]
    public function createIncidentsClientDecoratorReturnsAnInstanceOfIncidentsClientDecoratorCorrectly(): void
    {
        $incidentsClientDecorator = ClientFactory::createIncidentsClientDecorator(
            self::$server->getServerRoot() . '/',
            'fake_username',
            'fake_password',
        );

        self::assertInstanceOf(IncidentsClientDecorator::class, $incidentsClientDecorator);
    }

    #[Test]
    public function createDocumentsClientDecoratorReturnsAnInstanceOfDocumentsClientDecoratorCorrectly(): void
    {
        $documentsClientDecorator = ClientFactory::createDocumentsClientDecorator(
            self::$server->getServerRoot() . '/',
            'fake_username',
            'fake_password',
        );

        self::assertInstanceOf(DocumentsClientDecorator::class, $documentsClientDecorator);
    }
}
