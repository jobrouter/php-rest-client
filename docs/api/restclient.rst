.. include:: /_includes.rst.txt

.. _api-restclient:

==================
Client\\RestClient
==================

.. php:class:: final class Brotkrueml\JobRouterClient\Client\RestClient

   :implements: :ref:`Brotkrueml\\JobRouterClient\\Client\\ClientInterface <api-clientinterface>`

   Base REST client.

   .. php:method:: __construct($configuration)

      The constructor receives the configuration and authenticates immediately
      against the given JobRouter system.

      :param Brotkrueml\\JobRouterClient\\Configuration\\ClientConfiguration $configuration: The configuration object.

   .. php:method:: authenticate()

      Authenticate against the configured JobRouter system.

      :returns Brotkrueml\\JobRouterClient\\Client\\RestClient: An instance of the class itself.

   .. php:method:: request($method, $resource, $data = [])

      :param string $method: The HTTP method (e.g. ``GET``, ``POST``, ``PUT``, ``PATCH``, ``DELETE``)
      :param string $resource: The resource to call (e.g. ``application/sessions``)
      :param array $data: The data to send. It must be an array (or omit the parameter if no data is needed).
      :returns Psr\\Http\\Message\\ResponseInterface: A `PSR-7 response interface <https://www.php-fig.org/psr/psr-7/#33-psrhttpmessageresponseinterface>`_

   .. php:method:: getJobRouterVersion()

      :returns string: The version of the JobRouter system


Usage Example
-------------

::

   <?php
   use Brotkrueml\JobRouterClient\Client\RestClient;
   use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;

   require_once 'vendor/autoload.php';

   $configuration = new ClientConfiguration(
      'https://example.org/jobrouter/',
      'the_user',
      'the_password'
   );

   $client = new RestClient($configuration);

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

   echo $response->getStatusCode() . "\n";
   var_dump($response->getBody()->getContents());
