<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Tests\Unit\Service;

use TYPO3\CMS\Core\Resource\MimeTypeDetector;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use WebVision\MimeConverter\Exception\InsufficientFileExtensionException;
use WebVision\MimeConverter\Exception\MimeTypeNotRegisteredException;
use WebVision\MimeConverter\Service\MimeTypeDetectorService;

/**
 * @covers \WebVision\MimeConverter\Service\MimeTypeDetectorService
 */
class MimeTypeDetectorServiceTest extends UnitTestCase
{
    protected MimeTypeDetectorService $mimeTypeDetectorService;

    /**
     * @test
     * @dataProvider getExpectedMimeTypeFromFileSuffix
     * @covers       MimeTypeDetectorService::getExpectedMimeTypeFromFileSuffix
     */
    public function getExpectedMimeTypeFromFileSuffixMatchesCorrectMime(
        string $fileSuffix,
        string $expectedResult
    ): void {
        self::assertTrue(
            in_array(
                $expectedResult,
                $this->mimeTypeDetectorService->getExpectedMimeTypeFromFileSuffix($fileSuffix)
            )
        );
    }

    /**
     * Data Provider for getExpectedMimeTypeFromFileSuffixMatchesCorrectMime
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public function getExpectedMimeTypeFromFileSuffix(): array
    {
        return [
            'image matches mime type' => [
                'jpg',
                'image/jpeg',
            ],
            'video matches mime' => [
                'mp4',
                'video/mp4',
            ],
        ];
    }

    /**
     * @test
     */
    public function notExistentFileThrowsException(): void
    {
        $nonExistentFile = 'somewhere/notThere.jpg';

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionCode(1677576594374);

        $this->mimeTypeDetectorService->detectMimeTypeFromFile($nonExistentFile);
    }

    /**
     * @test
     */
    public function notExistentMimeThrowsException(): void
    {
        $mime = 'image/nonExistent';

        self::expectException(MimeTypeNotRegisteredException::class);
        self::expectExceptionCode(1677575729573);

        $this->mimeTypeDetectorService->getFileExtensionsForMimeType($mime);
    }

    /**
     * @test
     * @dataProvider getExtensionFromFile
     * @covers       MimeTypeDetectorService::getExtensionFromFile
     */
    public function getExtensionFromFileReturnsCorrectExtension(
        string $fileName,
        string $expectedResult
    ): void {
        self::assertSame(
            $expectedResult,
            $this->mimeTypeDetectorService->getExtensionFromFile($fileName)
        );
    }

    /**
     * Data Provider for getExtensionFromFileReturnsCorrectExtension
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public function getExtensionFromFile(): array
    {
        return [
            'xml' => [
                'test.xml',
                'xml',
            ],
            'png' => [
                'test.png',
                'png',
            ],
        ];
    }

    /**
     * @test
     * @covers MimeTypeDetectorService::getExpectedMimeTypeFromFileSuffix
     */
    public function wrongFileSuffixThrowsException(): void
    {
        $suffix = 'nonExistent';

        self::expectException(InsufficientFileExtensionException::class);
        self::expectExceptionCode(1677590606903);

        $this->mimeTypeDetectorService->getExpectedMimeTypeFromFileSuffix($suffix);
    }

    protected function setUp(): void
    {
        $mimeTypeDetector = new MimeTypeDetector();
        $this->mimeTypeDetectorService = new MimeTypeDetectorService($mimeTypeDetector);
        parent::setUp();
    }
}
