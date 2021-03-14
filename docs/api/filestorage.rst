.. include:: /_includes.rst.txt

.. _api-filestorage:

=====================
Resource\\FileStorage
=====================

.. php:class:: final Brotkrueml\\JobRouterClient\\Resource\\FileStorage

   A storage for :ref:`Brotkrueml\\JobRouterClient\\Resource\\File <api-file>` objects.

   :implements: `\\Countable <https://www.php.net/manual/en/class.countable.php>`_
   :implements: `\\Iterator <https://www.php.net/manual/en/class.iterator.php>`_

   .. php:method:: attach($file)

      Add a file to the storage.

      :param Brotkrueml\\JobRouterClient\\Resource\\FileInterface $file: The file to attach.

   .. php:method:: contains($file)

      Check, if the storage contains a specific file

      :param Brotkrueml\\JobRouterClient\\Resource\\FileInterface $file: The file to check.
      :returns bool: :php:`true`, if the storage contains the file, otherwise :php:`false`

   .. php:method:: detach($file)

      Remove a file from the storage.

      :param Brotkrueml\\JobRouterClient\\Resource\\FileInterface $file: The file to detach.


Usage Example
-------------

::

   <?php
   use Brotkrueml\JobRouterClient\Resource\FileStorage;

   require_once 'vendor/autoload.php';

   $file1 = new File('/path/to/file1.pdf');
   $file2 = new File('/path/to/file2.jpg');

   $storage = new FileStorage();
   $storage->attach($file1);
   $storage->attach($file2);

   foreach ($storage as $file) {
      // Output:
      // /path/to/file1.pdf
      // /path/to/file2.jpg
      echo $file->getPath() . "\n";
   }

   $storage->detach($file1);

   // Output: 1
   echo count($storage) . "\n";

   // Output: false
   var_dump($storage->contains($file1));
