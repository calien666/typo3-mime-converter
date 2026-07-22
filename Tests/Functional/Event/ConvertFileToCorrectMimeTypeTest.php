<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Tests\Functional\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\Enum\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use WebVision\MimeConverter\Converter\Provider\ImageConverterProvider;
use WebVision\MimeConverter\Event\ConvertFileToCorrectMimeType;

#[CoversClass(ConvertFileToCorrectMimeType::class)]
#[CoversClass(ImageConverterProvider::class)]
final class ConvertFileToCorrectMimeTypeTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/mime_converter/',
    ];

    protected array $pathsToProvideInTestInstance = [
        'typo3conf/ext/mime_converter/Tests/Functional/Fixtures/Folders/upload/' => 'fixture/',
    ];

    /**
     * @return \Generator<string, array{fixtureFileName: string, sourceMimeType: string, expectedMimeType: string}>
     */
    public static function mismatchedFileDataProvider(): \Generator
    {
        yield 'heic content named .jpg is re-encoded to jpeg' => [
            'fixtureFileName' => 'sample1.jpg',
            'sourceMimeType' => 'image/heic',
            'expectedMimeType' => 'image/jpeg',
        ];
        yield 'png content named .jpg is re-encoded to jpeg' => [
            'fixtureFileName' => 'pngAsJpg.jpg',
            'sourceMimeType' => 'image/png',
            'expectedMimeType' => 'image/jpeg',
        ];
        yield 'tiff content named .png is re-encoded to png' => [
            'fixtureFileName' => 'tiffAsPng.png',
            'sourceMimeType' => 'image/tiff',
            'expectedMimeType' => 'image/png',
        ];
    }

    #[Test]
    #[DataProvider('mismatchedFileDataProvider')]
    public function fileWithMismatchingMimeTypeIsConverted(
        string $fixtureFileName,
        string $sourceMimeType,
        string $expectedMimeType
    ): void {
        $sourceFile = Environment::getPublicPath() . '/fixture/' . $fixtureFileName;
        $this->assertSame($sourceMimeType, mime_content_type($sourceFile));

        $file = $this->addFixtureFileToDefaultStorage($sourceFile);

        $this->assertSame(
            $expectedMimeType,
            mime_content_type($file->getForLocalProcessing(false))
        );
    }

    #[Test]
    public function fileWithMatchingMimeTypeIsLeftUntouched(): void
    {
        $sourceFile = Environment::getPublicPath() . '/fixture/validImage.jpg';
        $this->assertSame('image/jpeg', mime_content_type($sourceFile));
        $originalHash = md5((string)file_get_contents($sourceFile));

        $file = $this->addFixtureFileToDefaultStorage($sourceFile);

        $this->assertSame('image/jpeg', mime_content_type($file->getForLocalProcessing(false)));
        $this->assertSame($originalHash, md5($file->getContents()));
    }

    private function addFixtureFileToDefaultStorage(string $sourceFile): FileInterface
    {
        /** @var ResourceStorage $resourceStorage */
        $resourceStorage = GeneralUtility::makeInstance(StorageRepository::class)->getDefaultStorage();

        return $resourceStorage->addFile(
            $sourceFile,
            $resourceStorage->getDefaultFolder(),
            '',
            DuplicationBehavior::REPLACE
        );
    }
}
