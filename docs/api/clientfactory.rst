.. include:: /_includes.rst.txt

.. _api-clientfactory:

=====================
Client\\ClientFactory
=====================

.. php:class:: final class Brotkrueml\JobRouterClient\Client\ClientFactory

   Factory for instantiating the rest client and the client decorators.

   .. php:staticmethod:: createDocumentsClientDecorator($baseUrl, $username, $password, $lifetime = 600)

      :param string $baseUrl: The base URL of the JobRouter system.
      :param string $username: The username, must not be empty.
      :param string $password: The password, must not be empty.
      :param int $lifetime: The lifetime in seconds. It must be between 0 and 3600.
      :returns Brotkrueml\\JobRouterClient\\Client\\DocumentsClientDecorator: The documents client decorator.

   .. php:staticmethod:: createIncidentsClientDecorator($baseUrl, $username, $password, $lifetime = 600)

      :param string $baseUrl: The base URL of the JobRouter system.
      :param string $username: The username, must not be empty.
      :param string $password: The password, must not be empty.
      :param int $lifetime: The lifetime in seconds. It must be between 0 and 3600.
      :returns Brotkrueml\\JobRouterClient\\Client\\IncidentsClientDecorator: The incidents client decorator.

   .. php:staticmethod:: createRestClient($baseUrl, $username, $password, $lifetime = 600)

      :param string $baseUrl: The base URL of the JobRouter system.
      :param string $username: The username, must not be empty.
      :param string $password: The password, must not be empty.
      :param int $lifetime: The lifetime in seconds. It must be between 0 and 3600.
      :returns Brotkrueml\\JobRouterClient\\Client\\RestClient: The rest client.

