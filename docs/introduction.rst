.. include:: /_includes.rst.txt

.. _introduction:

============
Introduction
============

`JobRouter® <https://www.jobrouter.com/>`_ is a scalable digitalisation platform
which links processes, data and documents. This JobRouter Client eases the
access to the REST API. The library supports JobRouter® versions 4.2-5.2.

The library can be used to automate tasks in PHP scripts like importing or
synchronising data in the JobData module, start processes or working with
archive documents. The authentication is done in the background so you
concentrate on your business domain.

The `PSR-7 standard <https://www.php-fig.org/psr/psr-7/>`_ is used, so you'll
get in touch with objects which implements the :php:`ResponseInterface`.
Currently, the JobRouter Client uses
`nyholm/psr7 <https://github.com/nyholm/psr7>`_ for the PSR-7 implementation
and `Buzz <https://github.com/kriswallsmith/buzz>`_ as HTTP client. But that
shouldn't bother you.

The library is available under the `GNU General Public License v2.0
<https://github.com/brotkrueml/jobrouter-client/blob/main/LICENSE.txt>`_. You
can also have a look into the `source code
<https://github.com/brotkrueml/jobrouter-client>`_ on GitHub or `file an issue
<https://github.com/brotkrueml/jobrouter-client/issues>`_.


Release Management
==================

This library uses `semantic versioning <https://semver.org/>`_ which basically
means for you, that

- Bugfix updates (e.g. `1.0.0` => `1.0.1`) just includes small bug fixes or
  security relevant stuff without breaking changes.
- Minor updates (e.g. `1.0.0` => `1.1.0`) includes new features and smaller
  tasks without breaking changes.
- Major updates (e.g. `1.0.0` => `2.0.0`) includes breaking changes which can be
  refactorings, features or bug fixes.

The changes are recorded in the `changelog
<https://github.com/brotkrueml/jobrouter-client/blob/main/CHANGELOG.md>`_.
