.. include:: /_includes.rst.txt

.. highlight:: shell

.. _installation:

============
Installation
============


.. _installation.requirements:

Requirements
============

The JobRouter Client |version| requires at least PHP 7.4; using the latest
version of PHP is highly recommended.

The library requires the curl, filter and json extensions, which are normally
enabled by default.

Version matrix
--------------

================ =========================== =========
JobRouter Client JobRouter®                  PHP
================ =========================== =========
1.0              4.2 - 5.1                   7.2 - 7.4
---------------- --------------------------- ---------
1.1              4.2 - 5.2                   7.3 - 8.1
---------------- --------------------------- ---------
1.2 / 1.3        4.2 - 5.2 / 2022.1 - 2022.2 7.4 - 8.1
---------------- --------------------------- ---------
1.4              4.2 - 5.2 / 2022.1 - 2022.4 7.4 - 8.2
================ =========================== =========

You can use, for example, JobRouter Client version 1.0 on JobRouter® version 5.2
at your own risk. However, new REST API resources may not be usable.


.. _installation.composer:

Composer Based Installation
===========================

Simply add a dependency on ``brotkrueml/jobrouter-client`` to your project's
:file:`composer.json` file if you use `Composer <https://getcomposer.org/>`_ to
manage the dependencies of your project::

   composer require brotkrueml/jobrouter-client

This is the preferred way: You can track new releases of the JobRouter Client
and the underlying libraries and update them yourself independently.


Manual Installation
===================

Download the recent version of the JobRouter Client from GitHub:

`<https://github.com/brotkrueml/jobrouter-client/releases>`_

Expand :guilabel:`Assets` and select the appropriate package (zip, tar.gz).
It is advised to check the integrity of the package:

Linux
-----

.. parsed-literal::

   sha256sum -c jobrouter-client-\ |release|\ .tar.gz.sha256.txt

It should output:

.. parsed-literal::

   jobrouter-client-\ |release|\ .tar.gz: OK


Windows
-------

Windows is shipped with the `certutil
<https://docs.microsoft.com/en-us/windows-server/administration/windows-commands/certutil>`_
program. You can check the hash of the file with:

.. parsed-literal::

   CertUtil -hashfile jobrouter-client-\ |release|\ .zip sha256

and compare it with the hash value in the corresponding
:file:`.sha256.txt` file.


.. important::
   The underlying libraries are only updated on new releases of the JobRouter
   Client.
