<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Converter\Provider;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebVision\MimeConverter\Converter\AbstractFileConverter;
use WebVision\MimeConverter\Utility\MimeTypeUtility;

class ImageConverterProvider extends AbstractFileConverter
{
    private string $sourceTemp;

    /**
     * @var array<string, string>
     */
    protected static array $forceConvertTypes = [
        'image/x-bmp' => 'bmp',
        'image/x-bitmap' => 'bmp',
        'image/x-xbitmap' => 'bmp',
        'image/x-win-bitmap' => 'bmp',
        'image/x-windows-bmp' => 'bmp',
        'image/ms-bmp' => 'bmp',
        'image/x-ms-bmp' => 'bmp',
        'image/cdr' => 'cdr',
        'image/x-cdr' => 'cdr',
        'image/gif' => 'gif',
        'image/x-icon' => 'ico',
        'image/x-ico' => 'ico',
        'image/vnd.microsoft.icon' => 'ico',
        'image/jp2' => 'jp2',
        'video/mj2' => 'jp2',
        'image/jpx' => 'jp2',
        'image/jpm' => 'jp2',
        'image/jpeg' => 'jpg',
        'image/heif' => 'heif',
        'image/heic' => 'heic',
        'image/heif-sequence' => 'heif',
        'image/heic-sequence' => 'heic',
        'image/pjpeg' => 'jpg',
        'image/png' => 'png',
        'image/x-png' => 'png',
        'image/vnd.adobe.photoshop' => 'psd',
        'image/svg+xml' => 'svg',
        'image/tiff' => 'tiff',
    ];

    public static function canConvert(string $mimeType, string $expectedMimeType): bool
    {
        return
            array_key_exists($mimeType, self::$forceConvertTypes)
            && array_key_exists($expectedMimeType, self::$forceConvertTypes);
    }

    public function convert(
        string $originalFile,
        string $setMimeType,
        string $expectedMimeType
    ): bool {
        $this->sourceTemp = GeneralUtility::tempnam(
            'converter',
            sprintf(
                '.%s',
                MimeTypeUtility::detectFileExtensionFromMimeType($setMimeType)[0] ?? ''
            )
        );

        copy($originalFile, $this->sourceTemp);

        $im = new \Imagick();
        try {
            $im->readImage($this->sourceTemp);
            $im->setFormat(self::$forceConvertTypes[$expectedMimeType]);
            $im->writeImage($originalFile);
        } catch (\ImagickException $e) {
            return false;
        } finally {
            $im->clear();
        }
        return true;
    }

    public function __destruct()
    {
        GeneralUtility::unlink_tempfile($this->sourceTemp);
    }
}
