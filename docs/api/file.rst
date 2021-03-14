.. include:: /_includes.rst.txt

.. _api-file:

==============
Resource\\File
==============

.. php:class:: final Brotkrueml\\JobRouterClient\\Resource\\File

   Value object that represents a file.

   .. php:method:: __construct($path, $fileName = '', $contentType = '')

      :param string $path: The full path to the file.
      :param string $fileName: The file name that should be used, if different from original file name.
      :param string $contentType: The content type, if different from file.

   .. php:method:: getContentType()

      Retrieve the content type.

      :returns string: The content type, empty string if not defined.

   .. php:method:: getFileName()

      Retrieve the file name.

      :returns string: The file name, empty string if not defined.

   .. php:method:: getPath()

      Retrieve the full path.

      :returns string: The full path.


Usage Example
-------------

::

   <?php
   use Brotkrueml\JobRouterClient\Resource\File;

   require_once 'vendor/autoload.php';

   $file1 = new File('/path/to/file.pdf');

   $file2 = new File('/path/to/file.pdf', 'invoice42.pdf');

   $file3 = new File('path/to/file', 'invoice42.pdf', 'application/pdf');
