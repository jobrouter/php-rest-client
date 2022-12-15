.. include:: /_includes.rst.txt

.. _upgrade:

=======
Upgrade
=======

From version 1.x to 2.0
=======================

With JobRouter Client 2.0 the minimum requirements have changed, supported are
now:

-  PHP ≥ 8.1
-  JobRouter® ≥ 2022.1


REST client
-----------

On instantiation of the :ref:`RestClient <api-restclient>` class no
authentication is performed automatically anymore. Call the
:php:`->authenticate()` method manually before sending a request to the REST
API.

The :php:`->authenticate()` method now returns an instance to the class itself.
This way, one can use a fluent interface:

.. code-block:: php

   $restClient = (new RestClient($configuration))->authenticate();

   // or:

   (new RestClient($configuration))
      ->authenticate()
      ->request($method, $resource);


API changes
-----------

-  :ref:`Incident <api-incident>` class:

   -  On instantiation the step number must be passed as argument in the
      constructor.
   -  The :php:`->getStep()` method now returns always an integer, previously it
      was an integer or null.
   -  The :php:`->getPool()` method now returns always an integer, previously it
      was an integer or null.
   -  The :php:`->isSimulation()` method now returns always a boolean,
      previously it was a boolean or null.
   -  The :php:`->setPriority()` method accepts only a :ref:`Priority
      <api-priority>` enum, previously it was an integer or null.
   -  The :php:`->getPriority()` method returns a :ref:`Priority
      <api-priority>` enum, previously it was an integer or null.
