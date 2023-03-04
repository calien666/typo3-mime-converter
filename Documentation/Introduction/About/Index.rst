.. include:: /Includes.rst.txt

.. _whatDoesItDo:

What does it do?
================

This extension provides automatic conversion of wrong suffixed images.

Example
-------

Your customer provides an image taken with iPhone, which is saved as
image type \*.heic/\*.heif. This is not possible to upload to TYPO3.
Customer changes file suffix from .heic to .jpg.
This will be uploaded to TYPO3, but metadata extractors can't handle
exif data.

To correct this behaviour, this extension ships an event listener
to check.

Workflow
--------

#. Event listener checks for suffix matching mime type
#. Listener calls FileConverterRegistry to detect possible Converter
#. Registry serves ConvertProvider
#. Listener calls ConvertProvider
#. ConvertProvider converts to mime type expected from file suffix
#. Uploaded file is overwritten by correct file matching mime type
