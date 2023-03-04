<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Tests\Functional\Converter;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use WebVision\MimeConverter\Converter\FileConverterRegistry;
use WebVision\MimeConverter\Converter\Provider\ImageConverterProvider;

/**
 * @covers \WebVision\MimeConverter\Converter\FileConverterRegistry
 */
class FileConverterRegistryTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/mime_converter/',
    ];

    /**
     * @test
     */
    public function providersLoaded(): void
    {
        $fileConverterRegistry = GeneralUtility::makeInstance(FileConverterRegistry::class);

        $converter = $fileConverterRegistry->findConverterForMimeType(
            'image/jpeg',
            'image/jpeg'
        );

        static::assertInstanceOf(ImageConverterProvider::class, $converter);
    }
}
