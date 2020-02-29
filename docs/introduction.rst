.. include:: Includes.txt

.. _introduction:

============
Introduction
============

`JobRouter <https://www.jobrouter.com/>`_ is a scalable digitisation platform
which links processes, data and documents. This JobRouter Client eases the
access to the REST API. The library supports JobRouter versions 4.2-5.0.

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

.. admonition:: Work In Progress

   Currently, the JobRouter Client is in a development phase. As it can be used
   already, the API is still subject to change.
