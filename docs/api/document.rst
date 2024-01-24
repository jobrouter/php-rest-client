.. include:: /_includes.rst.txt

.. _api-document:

===============
Model\\Document
===============

.. php:class:: final class JobRouter\AddOn\RestClient\Model\Document

   Document model class which collects the data for the an archive document.

   .. php:method:: addFile($file)

      Adds a file to the document.

      :param Brotkrueml\\JobRouterClient\\Resource\\FileInterface $file: The :ref:`File <api-file>` to add.
      :returns self: An instance of itself.

   .. php:method:: getFiles()

      Retrieve the files.

      :returns Brotkrueml\\JobRouterClient\\Resource\\FileStorage: A :ref:`FileStorage <api-filestorage>` object.

   .. php:method:: getIndexField($name)

      Retrieve the value of an index field.

      :param string $name: The name of the index field.
      :returns ?string: The value of the index field, or :php:`null` if not existing.

   .. php:method:: getKeywordField($name)

      Retrieve the value of a keyword field.

      :param string $name: The name of the keyword field.
      :returns ?string: The value of the keyword field, or :php:`null` if not existing.

   .. php:method:: setFiles($fileStorage)

      Sets the files defined in a :ref:`FileStorage <api-filestorage>`.

      :param Brotkrueml\\JobRouterClient\\Resource\\FileStorage $fileStorage: The :ref:`FileStorage <api-filestorage>`.
      :returns self: An instance of itself.

   .. php:method:: setIndexField($name, $value)

      Set the value of an index field.

      :param string $name: The name of the index field.
      :param string $value: The value of the index field.
      :returns self: An instance of itself.

   .. php:method:: setKeywordField($name, $value)

      Set the value of a keyword field.

      :param string $name: The name of the keyword field.
      :param string $value: The value of the keyword field.
      :returns self: An instance of itself.


Usage Example
-------------

::

   <?php
   use JobRouter\AddOn\RestClient\Model\Document;
   use JobRouter\AddOn\RestClient\Resource\File;

   require_once 'vendor/autoload.php';

   $document = new Document();
   $document
      ->setIndexField('COMPANY', 'Acme Ltd.')
      ->setIndexField('INVOICENR', 'IN02984')
      ->setKeywordField('KEYWORDS', 'acme')
      ->addFile(new File('/path/to/invoice/in02984.pdf'))
   ;
