.. include:: ../Includes.txt

.. _api-documentsclientdecorator:

================================
Client\\DocumentsClientDecorator
================================

.. php:class:: final Brotkrueml\\JobRouterClient\\Client\\DocumentsClientDecorator

   :extends: :ref:`Brotkrueml\\JobRouterClient\\Client\\ClientDecorator <api-clientdecorator>`

   Client for easing the archiving documents.

   .. php:method:: __construct($client)

      The constructor receives an instance of the RestClient or one of the
      decorators.

      :param Brotkrueml\\JobRouterClient\\Client\\ClientInterface $client: An instance of a client.

   .. php:method:: authenticate()

      Authenticate against the configured JobRouter system.

   .. php:method:: request($method, $resource, $data = [])

      :param string $method: The HTTP method (e.g. ``GET``, ``POST``, ``PUT``, ``PATCH``, ``DELETE``)
      :param string $resource: The resource to call (e.g. ``application/sessions``)
      :param $data: The data to send. This can be an instance of the Documents model.
      :returns Psr\\Http\\Message\\ResponseInterface: A `PSR-7 response interface <https://www.php-fig.org/psr/psr-7/#33-psrhttpmessageresponseinterface>`_

   .. php:method:: getJobRouterVersion()

      :returns string: The version of the JobRouter system


Usage Example
-------------

::

   <?php
   use Brotkrueml\JobRouterClient\Client\RestClient;
   use Brotkrueml\JobRouterClient\Client\DocumentsClientDecorator;
   use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
   use Brotkrueml\JobRouterClient\Model\Document;
   use Brotkrueml\JobRouterClient\Resource\File;

   require_once 'vendor/autoload.php';

   $configuration = new ClientConfiguration(
      'https://example.org/jobrouter/',
      'the_user',
      'the_password'
   );

   $client = new RestClient($configuration);
   $documentsClient = new DocumentsClientDecorator($client);

   $document = (new Document())
      ->setIndexField('INVOICENR', 'IN02984')
      ->addFile(new File('/path/to/invoice/in02984.pdf'));

   $response = $documentsClient->request(
      'POST',
      sprintf('application/jobarchive/archives/%s/documents', 'INVOICES'),
      $document
   );
