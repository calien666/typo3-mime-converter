<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Tests\Functional\Converter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use WebVision\MimeConverter\Converter\FileConverterRegistry;
use WebVision\MimeConverter\Converter\Provider\ImageConverterProvider;

#[CoversClass(FileConverterRegistry::class)]
final class FileConverterRegistryTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/mime_converter/',
    ];

    #[Test]
    public function providersLoaded(): void
    {
        $fileConverterRegistry = GeneralUtility::makeInstance(FileConverterRegistry::class);

        $converter = $fileConverterRegistry->findConverterForMimeType(
            'image/jpeg',
            'image/jpeg'
        );

        $this->assertInstanceOf(ImageConverterProvider::class, $converter);
    }
}
