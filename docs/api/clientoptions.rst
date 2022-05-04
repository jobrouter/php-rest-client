.. include:: /_includes.rst.txt

.. _api-clientoptions:

============================
Configuration\\ClientOptions
============================

.. php:class:: final Brotkrueml\JobRouterClient\Configuration\ClientOptions

   Immutable value object that represents the client options which is assigned
   to a :ref:`client configuration <api-clientconfiguration>`.

   .. php:method:: __construct($baseUrl, $username, $password)

      The constructor receives the available client options.

      :param bool $allowRedirects: Set to true, when redirects should be allowed. Default: *false*
      :param int $maxRedirects: Set the number of the maximum of redirects. Default: *5*
      :param int $timeout: The maximum number of seconds to execute. Default *0*
      :param bool $verify: Check the TLS certificate. Default: *true*
      :param string|null $proxy: A proxy URL. Default: *null*

Usage Example
-------------

::

   <?php
   use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
   use Brotkrueml\JobRouterClient\Configuration\ClientOptions;

   require_once 'vendor/autoload.php';

   $clientOptions = new ClientOptions(timeout: 10);

   $configuration = new ClientConfiguration(
      'https://example.org/jobrouter/',
      'the_user',
      'the_password'
   );

   // As ClientConfiguration is an immutable value object, a
   // new instance of the configuration object is returned
   $configuration = $configuration->withClientOptions($clientOptions);
