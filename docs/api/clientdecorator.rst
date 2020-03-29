.. _api-clientdecorator:

=======================
Client\\ClientDecorator
=======================

.. php:class:: abstract Brotkrueml\\JobRouterClient\\Client\\ClientDecorator

   :implements: :ref:`Brotkrueml\\JobRouterClient\\Client\\ClientInterface <api-clientinterface>`

   Abstract class for the client decorators.

   .. php:method:: __construct($client)

      The constructor receives an instance of the RestClient or one of the
      decorators.

      :param Brotkrueml\\JobRouterClient\\Client\\ClientInterface $client: An instance of a client.

   .. php:method:: authenticate()

      Authenticate against the configured JobRouter system.

   .. php:method:: request($method, $resource, $data = [])

      :param string $method: The HTTP method (e.g. ``GET``, ``POST``, ``PUT``, ``PATCH``, ``DELETE``)
      :param string $resource: The resource to call (e.g. ``application/sessions``)
      :param $data: The data to send
      :returns Psr\\Http\\Message\\ResponseInterface: A `PSR-7 response interface <https://www.php-fig.org/psr/psr-7/#33-psrhttpmessageresponseinterface>`_

   .. php:method:: getJobRouterVersion()

      :returns string: The version of the JobRouter system
