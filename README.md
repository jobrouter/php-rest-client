# JobRouter Client

[![CI Status](https://github.com/brotkrueml/jobrouter-client/workflows/CI/badge.svg?branch=master)](https://github.com/brotkrueml/jobrouter-client/actions?query=workflow%3ACI)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=jobrouter-client&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=jobrouter-client)
[![Coverage Status](https://coveralls.io/repos/github/brotkrueml/jobrouter-client/badge.svg?branch=master)](https://coveralls.io/github/brotkrueml/jobrouter-client?branch=master)

## Introduction

[JobRouter](https://www.jobrouter.com/) is a scalable digitization platform which links
processes, data and documents. This JobRouter client eases the access to the REST API.
The library supports JobRouter version 4.2 and up, as the token authentication is used.

At the current stage the authentication is done in the background so you concentrate on
the your business domain. Only JSON-related requests and responses are currently supported.

For the requests the [Symfony HTTP Client](https://symfony.com/doc/current/components/http_client.html)
is used where multiple requests can be done simultaneously.

The library can be used to automate tasks in PHP scripts like importing or synchronising
data in the JobData module or working with archive documents.

## Installation

The preferred way to install this library is with composer:

    composer req brotkrueml/jobrouter-client

## Usage

### Retrieve JobData table content

Let's start with an example, where you want to retrieve some information out of a JobData
table. Assuming this PHP script is in the root directory of your project:

    <?php
    use Brotkrueml\JobRouterClient\Client\RestClient;
    use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;

    require_once 'vendor/autoload.php';

    $configuration = new ClientConfiguration(
        'https://example.org/jobrouter/',
        'the_user',
        'the_password'
    );
    $configuration->setLifetime(30);

    try {
        $client = new RestClient($configuration);

        $response = $client->request(
            'application/jobdata/tables/FB6E9F2F-8486-8CD7-5FA5-640ACB9019E4/datasets',
            'GET'
        );

        echo $response->getStatusCode() . "\n";
        var_dump($response->getContent());
    } catch (RestClientException $e) {
        echo $e->getCode() . "\n";
        echo $e->getMessage();
    }

First, you have to instantiate a configuration class, which holds the base URI of
the JobRouter installation and the credentials for signing-in. Additionally, you can
define a lifetime of the JSON Web Token in seconds. If you don't define the lifetime,
a default value of 600 seconds will be used.

Second, create the REST client, you pass the configuration object for initialisation.
The authentication is done immediately, so you will get an exception if there is
something wrong with the credentials.

After the initialisation part you can now request the needed data or
store some data. You can make as many requests as you want, but keep in
mind: When the lifetime of the token is exceeded you will get an
authentication error. At this time, you have to handle it on your own. If this
happens, you can call at any time the authenticate method of the rest client:

    $restClient->authenticate();

You can do this also in advance to omit a timeout.

### Post a dataset to a JobData table

With the following request you can post a dataset to a JobData table:

    $response = $restClient->request(
        'application/jobdata/tables/FB6E9F2F-8486-8CD7-5FA5-640ACB9019E4/datasets',
        'POST',
        [
            'json' => [
                'dataset' => [
                    'column1' => 'content of column 1',
                    'column2' => 'content of column 2',
                ],
            ],
        ]
    );

    $statusCode = $response->getStatusCode();
    if ($statusCode === 201) {
        // Success
    } else {
        echo $statusCode . "\n";
        echo $response->getContent(false);
    }

Please keep in mind: You have to send all columns of a table for which
the user has the right to. Otherwise you will receive an error with
status code 422 (Unprocessable entity).

