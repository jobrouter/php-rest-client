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
   use Brotkrueml\JobRouterClient\Exception\AuthenticationException;
   use Brotkrueml\JobRouterClient\Exception\HttpException;

   require_once 'vendor/autoload.php';

   $configuration = new ClientConfiguration(
      'https://example.org/jobrouter/', // The base URL of the JobRouter installation
      'the_user', // The username
      'the_password' // The password
   );
   $configuration->setLifetime(30);

   try {
      $client = new RestClient($configuration);
   } catch (AuthenticationException|HttpException $e) {
      echo $e->getCode() . "\n";
      echo $e->getMessage() . "\n";

      if ($e->getPrevious()) {
         var_dump($e->getPrevious());
      }
   }

Let's dig into the piece of code:

#. Lines 2-5: The JobRouter client library uses the namespace
   :php:`Brotkrueml\JobRouterClient`, the :php:`uses` ease the writing of the
   following classes.

#. Line 7: Require the autoloading file, so the classes are found and can be
   used.

#. Lines 9-12: Define a :php:`ClientConfiguration` object with the base URL, the
   username and the password for your JobRouter installation.

#. Line 14: Set the lifetime in seconds of the JSON Web Token. The default
   value is 600 seconds - if you are fine with this, you can omit this setter.

#. Line 17: Now initialise the RestClient with the configuration object. During
   the initialisation the client will authenticate against the JobRouter
   installation.

#. Line 18: As there can be errors during the initialisation - like a typo in
   the base URL (throws an :php:`HttpException`) or wrong credentials (throws an
   :php:`AuthenticationException`)- embed the initialisation into a
   :php:`try`/:php:`catch` block. The thrown exception can embed another
   exception, you'll get it with :php:`->getPrevious()`.

After the initialisation part you can now request the needed data or store some
data. You can make as many requests as you want, but keep in mind: When the
lifetime of the token is exceeded you will get an authentication error.
For now, you have to handle it on your own. If this happens, you can call
at any time the :php:`authenticate()` method of the rest client:

::

   <?php
   // The JobRouter Client is already initialised

   $client->authenticate();

Call this also in advance to omit a timeout.


.. _usage.sending-requests:

Sending Requests
================

The :php:`RestClient` object exposes a :php:`request()` method to send a request
to the JobRouter REST API:

::

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

   The third parameter is optional. This is an array which can has one of two
   keys: :php:`json` for requests with the content type ``application/json`` and
   :php:`multipart` for the content type ``multipart/form-data``. In the
   sections above we will see how the array will be populated.


.. _usage.examples:

Examples
========

.. _usage.get-jobdata-dataset:

Retrieving a JobData Dataset
----------------------------

Let's start with an example to retrieve some data out of a JobData table. We
assume the client is already initialised, like in the
:ref:`introduction above <usage.initialisation>`.

::

   <?php
   // The JobRouter Client is already initialised

   try {
      $response = $client->request(
         'GET',
         'application/jobdata/tables/FB6E9F2F-8486-8CD7-5FA5-640ACB9019E4/datasets'
      );

      echo $response->getStatusCode() . "\n";
      var_dump($response->getBody()->getContents());
   } catch (AuthenticationException|HttpException $e) {
      // Error handling
   }

#. Lines 5-8: With the :php:`request()` method we'll send a request to the
   JobRouter installation. In this example, the method is ``GET`` as we want to
   retrieve data. The second parameter is the resource to the Jobdata module
   with the GUID of the table. The :php:`$response` is an object which
   implements the `Psr\Http\Message\ResponseInterface <https://www.php-fig.org/psr/psr-7/#33-psrhttpmessageresponseinterface>`_
   which itself extends the `Psr\Http\Message\MessageInterface <https://www.php-fig.org/psr/psr-7/#31-psrhttpmessagemessageinterface>`_

#. Line 10: The :php:`ResponseInterface` object exposes some methods. One is to
   get the status code.

#. Line 11: As the body of the response is returned as a stream you'll have
   to use :php:`->getBody()->getContents()` to retrieve a string of the
   response's body (in this case JSON-encoded).


.. _usage.post-jobdata-dataset:

Posting a JobData Dataset
-------------------------

With the following request you can post a dataset to a JobData table:

::

   <?php
   // The JobRouter Client is already initialised

   try {
      $response = $client->request(
         'POST',
         'application/jobdata/tables/FB6E9F2F-8486-8CD7-5FA5-640ACB9019E4/datasets',
         [
            'json' => [
               'dataset' => [
                  'column1' => 'content of column 1',
                  'column2' => 'content of column 2',
               ],
            ],
         ]
      );
   } catch (AuthenticationException|HttpException $e) {
      // Error handling
   }

#. Line 6: As we add a new dataset we have to use the ``POST`` method.
#. Lines 8-15: As third parameter of the :php:`request()` method the data is
   expected. As we will sent JSON-encoded data we use the :php:`json` key of
   the array. The sub array then holds the data expected by the resource.

.. important::

   You have to send all columns of a table for which the user has access rights.
   Otherwise you will receive an error with status code 422 (Unprocessable
   entity)!


.. _usage.starting-new-instance:

Starting a new instance of a process
------------------------------------

To start a new instance of a process you have to send the data as
``multipart/form-data`` instead of JSON like the previous examples:

::

   <?php
   // The JobRouter Client is already initialised

   // Define instance data
   $multipart = [
      'step' => '1',
      'summary' => 'Instance started via JobRouter Client',
      'processtable[fields][0][name]' => 'INVOICENR',
      'processtable[fields][0][value]' => 'IN02984',
      'processtable[fields][1][name]' => 'INVOICE_FILE',
      'processtable[fields][1][value]' => [
         'path'=>'/path/to/invoice/file.pdf',
         'filename' => 'in02984.pdf',
         // The content type is optional
         'contentType' => 'application/pdf',
      ],
   ];

   try {
      $response = $client->request(
         'POST',
         'application/incidents/invoice',
         ['multipart' => $multipart]
      );
   } catch (AuthenticationException|HttpException $e) {
      // Error handling
   }

#. Lines 5-17: Preparing the data to send as an array according to the JobRouter
   REST API documentation. To add a file set an array with the keys :php:`path`,
   :php:`filename` and :php:`contentType`. The last one is optional.

#. Lines 20-24: Send the data as ``multipart/form-data`` with the key
   :php:`multipart` in the array of the third argument.
