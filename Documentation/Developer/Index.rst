.. include:: /Includes.rst.txt

.. _developer:

Developer
=========

Adding own ConverterProvider
----------------------------

Adding an own ConverterProvider is easy:

.. code-block:: php

    <?php

    namespace MyVendor\MyExt\Converter\Provider;

    use WebVision\MimeConverter\Converter\AbstractFileConverter;

    class MyConverterProvider extends AbstractFileConverter
    {
        public static function canConvert(string $mimeType, string $expectedMimeType): bool
        {
            // check if converter can convert mime types
        }

        public function convert(
            string $originalFile,
            string $setMimeType,
            string $expectedMimeType
        ): bool {
            // convert file
        }

For help in conversion handling, look into
`WebVision\MimeConverter\Converter\Provider\ImageConverterProvider`
and use, if needed `WebVision\MimeConverter\Service\MimeTypeDetectorService`.

Register FileConverterProvider
------------------------------

Register your Provider inside `Services.yaml`

.. code-block:: yaml

    services:
      MyVendor\MyExt\Converter\Provider\MyConverterProvider:
        tags:
          - name: mime.converter
            identifier: my_identifier
