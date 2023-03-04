<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Tests\Functional\Event;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \WebVision\MimeConverter\Event\ConvertFileToCorrectMimeType
 * @covers \WebVision\MimeConverter\Converter\Provider\ImageConverterProvider
 */
class ConvertFileToCorrectMimeTypeTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/mime_converter/',
    ];

    protected array $pathsToProvideInTestInstance = [
        'typo3conf/ext/mime_converter/Tests/Functional/Fixtures/Folders/upload/' => 'fixture/',
    ];

    /**
     * @test
     */
    public function imageManipulatedIfMimeNotFileExtension(): void
    {
        $sourceFile = Environment::getPublicPath() . '/fixture/sample1.jpg';

        self::assertSame('image/heic', mime_content_type($sourceFile));

        /** @var ResourceStorage $resourceStorage */
        $resourceStorage = GeneralUtility::makeInstance(
            StorageRepository::class
        )->getDefaultStorage();
        $folder = $resourceStorage->getDefaultFolder();

        /** @var FileInterface $file */
        $file = $resourceStorage->addFile(
            $sourceFile,
            $folder,
            '',
            DuplicationBehavior::REPLACE
        );

        self::assertSame('image/jpeg', mime_content_type($file->getPublicUrl()));
    }
}
