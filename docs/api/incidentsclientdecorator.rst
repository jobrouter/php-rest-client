.. include:: /_includes.rst.txt

.. _api-incidentsclientdecorator:

================================
Client\\IncidentsClientDecorator
================================

.. php:class:: final class JobRouter\AddOn\RestClient\Exception\IncidentsClientDecorator

   :extends: :ref:`Brotkrueml\\JobRouterClient\\Client\\ClientDecorator <api-clientdecorator>`

   Client for easing the start of a process instance.

   .. php:method:: __construct($client)

      The constructor receives an instance of the RestClient or one of the
      decorators.

      :param Brotkrueml\\JobRouterClient\\Client\\ClientInterface $client: An instance of a client.

   .. php:method:: authenticate()

      Authenticate against the configured JobRouter system.

   .. php:method:: request($method, $resource, $data = [])

      :param string $method: The HTTP method (e.g. ``GET``, ``POST``, ``PUT``, ``PATCH``, ``DELETE``)
      :param string $resource: The resource to call (e.g. ``application/sessions``)
      :param $data: The data to send. This can be an instance of the Incident model.
      :returns Psr\\Http\\Message\\ResponseInterface: A `PSR-7 response interface <https://www.php-fig.org/psr/psr-7/#33-psrhttpmessageresponseinterface>`_

   .. php:method:: getJobRouterVersion()

      :returns string: The version of the JobRouter system


Usage Example
-------------

::

   <?php
   use JobRouter\AddOn\RestClient\Client\RestClient;
   use JobRouter\AddOn\RestClient\Client\IncidentsClientDecorator;
   use JobRouter\AddOn\RestClient\Configuration\ClientConfiguration;
   use JobRouter\AddOn\RestClient\Model\Incident;
   use JobRouter\AddOn\RestClient\Resource\File;

   require_once 'vendor/autoload.php';

   $configuration = new ClientConfiguration(
      'https://example.org/jobrouter/',
      'the_user',
      'the_password'
   );

   $client = new RestClient($configuration);
   $incidentsClient = new IncidentsClientDecorator($client);

   $incident = (new Incident())
      ->setStep(1)
      ->setSummary('Instance started via IncidentsClientDecorator')
      ->setProcessTableField('INVOICENR', 'IN02984')
      ->setProcessTableField(
         'INVOICE_FILE',
         new File('/path/to/invoice/file.pdf', 'in02984.pdf')
      )
   ;

   $response = $incidentsClient->request(
      'POST',
      'application/incidents/invoice',
      $incident
   );
