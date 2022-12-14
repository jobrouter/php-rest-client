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

API changes
-----------

-  :ref:`Incident <api-incident>` class:

   -  The :php:`->setPriority()` method accepts only a :ref:`Priority
      <api-priority>` enum, previously it was an integer or null.
   -  The :php:`->getPriority()` method returns a :ref:`Priority
      <api-priority>` enum, previously it was an integer or null.
