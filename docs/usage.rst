.. include:: Includes.txt

.. _usage:

=====
Usage
=====


.. _usage.initialisation:

Initialisation of the JobRouter Client
======================================

::

   <?php
   use Brotkrueml\JobRouterClient\Client\RestClient;
   use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
   use Brotkrueml\JobRouterClient\Exception\ExceptionInterface;

   require_once 'vendor/autoload.php';

   $configuration = new ClientConfiguration(
      'https://example.org/jobrouter/', // The base URL of the JobRouter installation
      'the_user', // The username
      'the_password' // The password
   );
   $configuration = $configuration->withLifetime(30);

   try {
      $client = new RestClient($configuration);
   } catch (ExceptionInterface $e) {
      echo $e->getCode() . "\n";
      echo $e->getMessage() . "\n";

      if ($e->getPrevious()) {
         var_dump($e->getPrevious());
      }
   }

Let's dig into the piece of code:

#. Lines 2-4: The JobRouter client library uses the namespace
   :php:`Brotkrueml\JobRouterClient`, the :php:`uses` ease the using of the
   following classes.

#. Line 6: Require the autoloading file, so the classes are found and can be
   used.

#. Lines 8-11: Define a :php:`ClientConfiguration` object with the base URL, the
   username and the password for your JobRouter installation.

#. Line 13: Overrides the default lifetime of the JSON Web Token in seconds.
   The default value is 600 seconds - if you are fine with this, you can omit
   this. As the configuration object is immutable, a new instance of the
   configuration is returned.

#. Line 16: Now instantiate the RestClient with the configuration object. During
   the instantiation the client will authenticate against the JobRouter
   installation.

#. Line 17: As there can be errors during the initialisation - like a typo in
   the base URL or wrong credentials embed the initialisation into a
   :php:`try`/:php:`catch` block. The thrown exception is by default an
   implementation of the :php:`ExceptionInterface`. It encapsulates sometimes
   another exception, you'll get it with :php:`->getPrevious()`. Of course, you
   can also catch by :php:`\Exception` or :php:`\Throwable`.

After the initialisation part you can now request the needed data or store some
data. You can make as many requests as you want, but keep in mind: When the
lifetime of the token is exceeded you will get an authentication error.
For now, you have to handle it on your own. If this happens, you can call
at any time the :php:`authenticate()` method of the rest client::

   <?php
   // The JobRouter Client is already initialised

   $client->authenticate();

Call this also in advance to omit a timeout.


.. _usage.get-jobrouter-version:

Retrieve the JobRouter version
==============================

Sometimes it can be handy to know the JobRouter version. The version number
can be retrieved with a :php:`RestClient` method::

   <?php
   // The JobRouter Client is already initialised

   $client->getJobRouterVersion();


.. _usage.sending-requests:

Sending Requests
================

The :php:`RestClient` object exposes a :php:`request()` method to send a request
to the JobRouter REST API::

   <?php
   // The JobRouter Client is already initialised

   $response = $client->request(
      $method,
      $resource,
      $data
   );

:aspect:`method`

   The method can be every available HTTP verb, like ``GET``, ``POST``, ``PUT``
   or ``DELETE`` that is available to the requested resource.

:aspect:`resource`

   The resource without the base URL and the API path, e.g.
   ``application/sessions`` to retrieve the session of the current user.

:aspect:`data`

   The third parameter is optional. This is an array which holds the data to be
   send along with the request.


.. _usage.examples:

Examples
========

.. _usage.get-jobdata-dataset:

Retrieving a JobData Dataset
----------------------------

Let's start with an example to retrieve some data out of a JobData table. We
assume the client is already initialised, like in the
:ref:`introduction above <usage.initialisation>`::

   <?php
   // The JobRouter Client is already initialised

   try {
      $response = $client->request(
         'GET',
         'application/jobdata/tables/FB6E9F2F-8486-8CD7-5FA5-640ACB9019E4/datasets'
      );

      echo $response->getStatusCode() . "\n";
      var_dump($response->getBody()->getContents());
   } catch (ExceptionInterface $e) {
      // Error handling
   }

#. Lines 5-8: With the :php:`request()` method we'll send a request to the
   JobRouter installation. In this example, the method is ``GET`` as we want to
   retrieve data. The second parameter is the resource to the Jobdata module
   with the GUID of the table. The :php:`$response` is an object which
   implements the `Psr\\Http\\Message\\ResponseInterface <https://www.php-fig.org/psr/psr-7/#33-psrhttpmessageresponseinterface>`_
   which itself extends the `Psr\\Http\\Message\\MessageInterface <https://www.php-fig.org/psr/psr-7/#31-psrhttpmessagemessageinterface>`_

#. Line 10: The :php:`ResponseInterface` object exposes some methods. One is to
   get the status code.

#. Line 11: As the body of the response is returned as a stream you'll have
   to use :php:`->getBody()->getContents()` to retrieve a string of the
   response's body (in this case JSON-encoded).


.. _usage.post-jobdata-dataset:

Posting a JobData Dataset
-------------------------

With the following request you can post a dataset to a JobData table::

   <?php
   // The JobRouter Client is already initialised

   try {
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

#. Line 6: As we add a new dataset we have to use the ``POST`` method.
#. Lines 8-13: As third parameter of the :php:`request()` method the data is
   defines to sent with the request.

.. important::

   Prior to JobRouter 5.0.8 you have to send all columns of a table for which
   the user has access rights. Otherwise you will receive an error with status
   code 422 (Unprocessable entity).


.. _usage.starting-new-instance:

Starting a new instance of a process
------------------------------------

To start a new instance of a process you have to send the data as
``multipart/form-data`` instead of JSON like the previous examples::

   <?php
   use Brotkrueml\JobRouterClient\Resource\File;

   // The JobRouter Client is already initialised

   // Define instance data
   $multipart = [
      'step' => '1',
      'summary' => 'Instance started via JobRouter Client',
      'processtable[fields][0][name]' => 'INVOICENR',
      'processtable[fields][0][value]' => 'IN02984',
      'processtable[fields][1][name]' => 'INVOICE_FILE',
      'processtable[fields][1][value]' => new File(
         '/path/to/invoice/file.pdf', // Full path to the file
         'in02984.pdf' // Optional: Use another file name for storing in JobRouter
         'contentType' => 'application/pdf', // Optional: The content type
      ),
   ];

   try {
      $response = $client->request(
         'POST',
         'application/incidents/invoice',
         $multipart
      );
   } catch (ExceptionInterface $e) {
      // Error handling
   }

#. Lines 5-18: Preparing the data to send as an array according to the JobRouter
   REST API documentation. To add a file instantiate a
   :php:`Brotkrueml\JobRouterClient\Resource\File` object. The first argument
   receives the full path to the file, the other two are optional: You can
   overwrite the file name and specify a content type.

#. Lines 21-25: Send the data.

But instead of having the hassle with the complex ``processtable`` and
``subtable`` structure just use the :php:`IncidentsClientDecorator` which gives you an
API to handle all the process table and sub table stuff::

   <?php
   // Additional uses
   use Brotkrueml\JobRouterClient\Client\IncidentsClientDecorator;
   use Brotkrueml\JobRouterClient\Model\Incident;
   use Brotkrueml\JobRouterClient\Resource\File;

   // The JobRouter Client is already initialised

   $incident = (new Incident())
      ->setStep(1)
      ->setSummary('Instance started via IncidentsClientDecorator')
      ->setProcessTableField('INVOICENR', 'IN02984')
      ->setProcessTableField(
         'INVOICE_FILE',
         new File(
            '/path/to/invoice/file.pdf', // Full path to the file
            'in02984.pdf' // Optional: Use another file name for storing in JobRouter
            'contentType' => 'application/pdf', // Optional: The content type
         )
      )
   ;

   try {
      $incidentsClient = new IncidentsClientDecorator($client);

      $response = $incidentsClient->request(
         'POST',
         'application/incidents/invoice',
         $incident
      );
   } catch (ExceptionInterface $e) {
      // Error handling
   }

This is much more intuitive. So, let's have a look:

#. Lines 9-21: Create an object instance of the :php:`Incident` model and use the
   available setters to assign the necessary data.

#. Line 24: Create the :php:`IncidentsClientDecorator`. As an argument it gets an
   already initialised :php:`RestClient` instance. It is a decorator for the
   Rest Client, so you can also use it to authenticate or make other requests,
   e.g. to the JobData module.

#. Lines 26-30: Use the :php:`Incident` model as third argument for the
   :php:`request()` method. As usual you'll get a :php:`ResponseInterface`
   object back with the response of the HTTP request. If you would pass an array
   the request is passed unaltered to the Rest Client.
