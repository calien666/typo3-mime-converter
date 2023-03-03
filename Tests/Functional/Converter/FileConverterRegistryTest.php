<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Tests\Functional\Converter;

use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
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

    protected array $pathsToProvideInTestInstance = [
        'typo3conf/ext/mime_converter/Tests/Functional/Fixtures/Folders/upload/' => 'fixture/',
    ];

    protected function setUp(): void
    {
        parent::setUp();
    }

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

        self::assertInstanceOf(ImageConverterProvider::class, $converter);
    }

    /**
     * @test
     */
    public function imageManipulatedIfMimeNotFileExtension(): void
    {
        /** @var string $sourceFile */
        $sourceFile = realpath('fixture/sample1.jpg');

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
