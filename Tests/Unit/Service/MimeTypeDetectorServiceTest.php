<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Resource\MimeTypeDetector;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use WebVision\MimeConverter\Exception\InsufficientFileExtensionException;
use WebVision\MimeConverter\Exception\MimeTypeNotRegisteredException;
use WebVision\MimeConverter\Service\MimeTypeDetectorService;

#[CoversClass(MimeTypeDetectorService::class)]
final class MimeTypeDetectorServiceTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('expectedMimeTypeFromFileSuffixDataProvider')]
    public function getExpectedMimeTypeFromFileSuffixMatchesCorrectMime(
        string $fileSuffix,
        string $expectedResult
    ): void {
        $subject = new MimeTypeDetectorService(new MimeTypeDetector());

        $this->assertContains(
            $expectedResult,
            $subject->getExpectedMimeTypeFromFileSuffix($fileSuffix)
        );
    }

    /**
     * @return \Generator<string, array{fileSuffix: string, expectedResult: string}>
     */
    public static function expectedMimeTypeFromFileSuffixDataProvider(): \Generator
    {
        yield 'image matches mime type' => [
            'fileSuffix' => 'jpg',
            'expectedResult' => 'image/jpeg',
        ];
        yield 'video matches mime type' => [
            'fileSuffix' => 'mp4',
            'expectedResult' => 'video/mp4',
        ];
    }

    #[Test]
    public function notExistentFileThrowsException(): void
    {
        $subject = new MimeTypeDetectorService(new MimeTypeDetector());

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionCode(1677576594374);

        $subject->detectMimeTypeFromFile('somewhere/notThere.jpg');
    }

    #[Test]
    public function notExistentMimeThrowsException(): void
    {
        $subject = new MimeTypeDetectorService(new MimeTypeDetector());

        self::expectException(MimeTypeNotRegisteredException::class);
        self::expectExceptionCode(1677575729573);

        $subject->getFileExtensionsForMimeType('image/nonExistent');
    }

    #[Test]
    #[DataProvider('extensionFromFileDataProvider')]
    public function getExtensionFromFileReturnsCorrectExtension(
        string $fileName,
        string $expectedResult
    ): void {
        $subject = new MimeTypeDetectorService(new MimeTypeDetector());

        $this->assertSame(
            $expectedResult,
            $subject->getExtensionFromFile($fileName)
        );
    }

    /**
     * @return \Generator<string, array{fileName: string, expectedResult: string}>
     */
    public static function extensionFromFileDataProvider(): \Generator
    {
        yield 'xml' => [
            'fileName' => 'test.xml',
            'expectedResult' => 'xml',
        ];
        yield 'png' => [
            'fileName' => 'test.png',
            'expectedResult' => 'png',
        ];
    }

    #[Test]
    public function wrongFileSuffixThrowsException(): void
    {
        $subject = new MimeTypeDetectorService(new MimeTypeDetector());

        self::expectException(InsufficientFileExtensionException::class);
        self::expectExceptionCode(1677590606903);

        $subject->getExpectedMimeTypeFromFileSuffix('nonExistent');
    }
}
