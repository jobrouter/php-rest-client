# Usage

* [Initialisation](#initialisation)
* [Retrieve the JobRouter version](#retrieve-the-jobrouter-version)
* [Sending requests](#sending-requests)
* [Examples](#examples)
  * [Retrieving a JobData dataset](#retrieving-a-jobdata-dataset)
  * [Posting a JobData dataset](#posting-a-jobdata-dataset)
  * [Starting a new instance of a process](#starting-a-new-instance-of-a-process)
  * [Archiving a Document](#archiving-a-document)
  * [Nesting of client decorators](#nesting-of-client-decorators)
  * [Using a factory to create a client](#using-a-factory-to-create-a-client)

> If you find a bug or want to propose a feature, please use the
> [issue tracker on GitHub](https://github.com/jobrouter/php-rest-client/issues).

## Initialisation

```php
<?php
use JobRouter\AddOn\RestClient\Client\RestClient;
use JobRouter\AddOn\RestClient\Configuration\ClientConfiguration;
use JobRouter\AddOn\RestClient\Exception\ExceptionInterface;

// Require the autoloading file, so the classes are found and can be used.
require_once 'vendor/autoload.php';

// Define a ClientConfiguration object with the base URL, the username and
// the password for your JobRouter® installation.
$configuration = new ClientConfiguration(
    'https://example.org/jobrouter/', // The base URL of the JobRouter installation
    'the_user', // The username
    'the_password' // The password
);

// Overrides the default lifetime of the JSON Web Token in seconds.
// The default value is 600 seconds - if you are fine with this, you can omit
// calling the method. As the configuration object is immutable, a new instance
// of the configuration is returned.
$configuration = $configuration->withLifetime(30);

// Now instantiate the RestClient with the configuration object.
$client = new RestClient($configuration);

try {
// To authenticate against the configured JobRouter® installation the
// authenticate() method is called. As there can be errors during the
// authentication like a typo in the base URL or wrong credentials, embed the
// authenticate call into a try/catch block.
    $client->authenticate();
} catch (ExceptionInterface $e) {
    // The thrown exception is by default an implementation of the
    // ExceptionInterface.
    echo $e->getCode() . "\n";
    echo $e->getMessage() . "\n";

    // The exception encapsulates sometimes another exception, you will get
    // it with calling getPrevious() on the exception. Of course, you can
    // also catch by \Exception or \Throwable.
    if ($e->getPrevious()) {
        var_dump($e->getPrevious());
    }
}
```

After the initialisation part you can now request the needed data or store some
data. You can make as many requests as you want, but keep in mind: When the
lifetime of the token is exceeded you will get an authentication error.
If this happens, you can call at any time the `authenticate()` method of
the REST client again. You can call this also in advance to omit a timeout.


## Retrieve the JobRouter version

Sometimes it can be handy to know the JobRouter® version. The version number
can be retrieved with a `RestClient` method::

```php
<?php
// The JobRouter REST Client is already initialised

$client->getJobRouterVersion();
```

**Note:** The version is only available after a successful authentication.
Directly after instantiation of the REST client the returned version is an
empty string.


## Sending Requests

The `RestClient` object exposes a `request()` method to send a request to the
JobRouter® REST API:

```php
<?php
// The JobRouter REST Client is already initialised

$response = $client->request(
    $method,
    $resource,
    $data
);
```

`$method`: The method can be every available HTTP verb, like `GET`, `POST`, `PUT`
or `DELETE` that is available to the requested resource.

`$resource`: The resource without the base URL and the API path, for example,
`application/sessions` to retrieve the session of the current user.

`$data`: The third parameter is optional. This is an array which holds the data to be
sent along with the request.

## Examples

### Retrieving a JobData dataset

Let's start with an example to retrieve some data out of a JobData table. We
assume the client is already initialised, like in the
[introduction above](#initialisation):

```php
<?php
// The JobRouter REST Client is already initialised

try {
    // With the request()` method we'll send a request to the JobRouter®
    // installation. In this example, the method is GET as we want to
    // retrieve data. The second parameter is the resource to the JobData module
    // with the GUID of the table.
    $response = $client->request(
        'GET',
        'application/jobdata/tables/FB6E9F2F-8486-8CD7-5FA5-640ACB9019E4/datasets'
    );

    echo $response->getStatusCode() . "\n";

    //the body of the response is returned as a stream, you'll have
    // to use ->getBody()->getContents() to retrieve a string of the
    // response's body (in this case JSON-encoded).
    var_dump($response->getBody()->getContents());
} catch (ExceptionInterface $e) {
    // Error handling
}
```

The `$response` variable is an object which implements the
[\Psr\Http\Message\ResponseInterface](https://www.php-fig.org/psr/psr-7/#33-psrhttpmessageresponseinterface).

### Posting a JobData dataset

With the following request you can post a dataset to a JobData table:

```php
<?php
// The JobRouter REST Client is already initialised

try {
    // As we add a new dataset we have to use the POST method.
    $response = $client->request(
    'POST',
    'application/jobdata/tables/FB6E9F2F-8486-8CD7-5FA5-640ACB9019E4/datasets',
    [
        'dataset' => [
            'column1' => 'content of column 1',
            'column2' => 'content of column 2',
        ],
    ]
    );
} catch (ExceptionInterface $e) {
  // Error handling
}
```

### Starting a new instance of a process

To start a new instance of a process you have to send the data as
`multipart/form-data` instead of JSON like the previous examples.

Preparing the data to send as an array according to the JobRouter®
REST API documentation. To add a file instantiate a
`JobRouter\AddOn\RestClient\Resource\File` object. The
first argument receives the full path to the file, the other two are
optional: You can overwrite the file name and specify a content type.

```php
<?php
use JobRouter\AddOn\RestClient\Resource\File;

// The JobRouter REST Client is already initialised

// Define instance data
$multipart = [
    'step' => '1',
    'summary' => 'Instance started via JobRouter REST Client',
    'processtable[fields][0][name]' => 'INVOICENR',
    'processtable[fields][0][value]' => 'IN02984',
    'processtable[fields][1][name]' => 'INVOICE_FILE',
    'processtable[fields][1][value]' => new File(
        '/path/to/invoice/file.pdf', // Full path to the file
        'in02984.pdf', // Optional: Use another file name for storing in JobRouter
        'contentType' => 'application/pdf' // Optional: The content type
    ),
];

try {
    // Send the data
    $response = $client->request(
        'POST',
        'application/incidents/invoice',
        $multipart
    );
} catch (ExceptionInterface $e) {
    // Error handling
}
```


But instead of having the hassle with the complex `processtable` and
`subtable` structure just use the `IncidentsClientDecorator` which gives
you an API to handle all the process table and sub table stuff:

```php
<?php
// Additional uses
use JobRouter\AddOn\RestClient\Client\IncidentsClientDecorator;
use JobRouter\AddOn\RestClient\Model\Incident;
use JobRouter\AddOn\RestClient\Resource\File;

// The JobRouter REST Client is already initialised

// Create an object instance of the Incident model and use the
// available setters to assign the necessary data.
$incident = (new Incident())
    ->setStep(1)
    ->setSummary('Instance started via IncidentsClientDecorator')
    ->setProcessTableField('INVOICENR', 'IN02984')
    ->setProcessTableField(
        'INVOICE_FILE',
        new File(
            '/path/to/invoice/file.pdf', // Full path to the file
            'in02984.pdf', // Optional: Use another file name for storing in JobRouter®
            'contentType' => 'application/pdf' // Optional: The content type
        )
    )
;

try {
    // Create the IncidentsClientDecorator. As an argument it gets an
    // already initialised RestClient instance. It is a decorator for the
    // REST Client, so you can also use it to authenticate or make other
    // requests, for example, to the JobData module.
    $incidentsClient = new IncidentsClientDecorator($client);

    // Use the Incident model as third argument for the request() method.
    // As usual you'll get a ResponseInterface object back with the response
    // of the HTTP request. If you passed an array the request is passed
    // unaltered to the REST Client.
    $response = $incidentsClient->request(
        'POST',
        'application/incidents/invoice',
        $incident
    );
} catch (ExceptionInterface $e) {
    // Error handling
}
```

### Archiving a document

```php
<?php
use JobRouter\AddOn\RestClient\Resource\File;

// The JobRouter REST Client is already initialised

// Define document data
$documentContentAndMetaData = [
    'indexFields[0][name]' => 'INVOICENR',
    'indexFields[0][value]' => 'IN02984',
    'files[0]' => new File('/path/to/invoice/in02984.pdf'),
];

try {
    $response = $client->request(
        'POST',
        sprintf('application/jobarchive/archives/%s/documents', 'INVOICES'),
        $documentContentAndMetaData
    );
} catch (ExceptionInterface $e) {
    // Error handling
}
```

You can also use the `DocumentsClientDecorator` which eases the handling of the
multipart array:

```php
<?php
// Additional uses
use JobRouter\AddOn\RestClient\Client\DocumentsClientDecorator;
use JobRouter\AddOn\RestClient\Model\Document;
use JobRouter\AddOn\RestClient\Resource\File;

// The JobRouter REST Client is already initialised

$document = (new Document())
    ->setIndexField('INVOICENR', 'IN02984')
    ->addFile(new File('/path/to/invoice/in02984.pdf'));

try {
    $documentsClient = new DocumentsClientDecorator($client);

    $response = $documentsClient->request(
        'POST',
        sprintf('application/jobarchive/archives/%s/documents', 'INVOICES'),
        $document
    );
} catch (ExceptionInterface $e) {
    // Error handling
}
```


### Nesting of client decorators

The decorators can be nested. This can be useful when, for example, posting to a
JobData table, then archiving a document and at last starting an instance:

```php
<?php
use JobRouter\AddOn\RestClient\Client\DocumentsClientDecorator;
use JobRouter\AddOn\RestClient\Client\IncidentsClientDecorator;
use JobRouter\AddOn\RestClient\Client\RestClient;
use JobRouter\AddOn\RestClient\Configuration\ClientConfiguration;
use JobRouter\AddOn\RestClient\Exception\ExceptionInterface;

require_once 'vendor/autoload.php';

$configuration = new ClientConfiguration(
    'https://example.org/jobrouter/',
    'the_user',
    'the_password'
);

$restClient = new RestClient($configuration);
$incidentsClient = new IncidentsClientDecorator($restClient);
$overallClient = new DocumentsClientDecorator($incidentsClient);

// Now you can define an Incident and add it to the overallClient
```

### Using a factory to create a client

To simplify the instantiation of the different clients, you can use the
`ClientFactory` that creates them for you. The `RestClient` can be created with:

```php
<?php
use JobRouter\AddOn\RestClient\Client\ClientFactory;

$client = ClientFactory::createRestClient(
    'https://example.org/jobrouter/',
    'the_user',
    'the_password',
    30
);
```

The decorators can also be instantiated with factory methods. This is useful
when client nesting is not required:

```php
<?php
use JobRouter\AddOn\RestClient\Client\ClientFactory;

$incidentsClientDecorator = ClientFactory::createIncidentsClientDecorator(
    'https://example.org/jobrouter/',
    'the_user',
    'the_password',
    30
);

$documentsClientDecorator = ClientFactory::createDocumentsClientDecorator(
    'https://example.org/jobrouter/',
    'the_user',
    'the_password',
    30
);
```
