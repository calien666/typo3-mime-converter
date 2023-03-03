<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Tests\Functional\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use WebVision\MimeConverter\Exception\InsufficientFileExtensionException;
use WebVision\MimeConverter\Service\MimeTypeDetectorService;

/**
 * @covers \WebVision\MimeConverter\Service\MimeTypeDetectorService
 */
class MimeTypeDetectorServiceTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/mime_converter',
    ];

    protected array $configurationToUseInTestInstance = [
        'DB' => [
            'Connections' => [
                'Default' => [
                    'host' => 'db',
                    'password' => 'root',
                    'user' => 'root',
                ],
            ],
        ],
    ];

    /**
     * @test
     */
    public function fileExtensionsMatchCorrectMimeType(): void
    {
        $jpegSuffix = 'jpg';

        $service = GeneralUtility::makeInstance(MimeTypeDetectorService::class);

        $jpegMime = $service->getExpectedMimeTypeFromFileSuffix($jpegSuffix);

        self::assertIsArray($jpegMime);
        self::assertTrue(in_array('image/jpeg', $jpegMime));
    }

    /**
     * @test
     */
    public function wrongFileSuffixThrowsException(): void
    {
        $suffix = 'nonExistent';

        $service = GeneralUtility::makeInstance(MimeTypeDetectorService::class);

        self::expectException(InsufficientFileExtensionException::class);
        self::expectExceptionCode(1677590606903);
        $mime = $service->getExpectedMimeTypeFromFileSuffix($suffix);
    }
}
