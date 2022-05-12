.. include:: /_includes.rst.txt

.. _api-clientinterface:

=======================
Client\\ClientInterface
=======================

.. php:class:: interface Brotkrueml\JobRouterClient\Client\ClientInterface

   Interface for a REST client.

   .. php:method:: authenticate()

      Authenticate against the configured JobRouter system.

   .. php:method:: request($method, $resource, $data = [])

      Send a request to the JobRouter system.

      :param string $method: The HTTP method (e.g. ``GET``, ``POST``, ``PUT``, ``PATCH``, ``DELETE``).
      :param string $resource: The resource to call (e.g. ``application/sessions``).
      :param $data: The data to send.
      :returns Psr\\Http\\Message\\ResponseInterface: A `PSR-7 response interface <https://www.php-fig.org/psr/psr-7/#33-psrhttpmessageresponseinterface>`_.

   .. php:method:: getJobRouterVersion()

      Retrieve the version of the JobRouter system.

      :returns string: The version of the JobRouter system.
