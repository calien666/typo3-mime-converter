<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Converter\Provider;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebVision\MimeConverter\Converter\AbstractFileConverter;
use WebVision\MimeConverter\Exception\MimeTypeNotRegisteredException;

/**
 * @internal
 * This class is internal use for mime_converter and no public API
 *
 * Handles the image conversion for wrong suffixed images
 * @example for other mime converters
 */
#[AutoconfigureTag(
    'mime.converter',
    ['identifier' => 'image'],
)]
final class ImageConverterProvider extends AbstractFileConverter
{
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

    /**
     * Re-encodes the file into the expected image format with the ImageMagick "convert" binary.
     * A plain convert keeps embedded profiles (EXIF) and does not re-orient the image, so it
     * mirrors the former \Imagick based implementation. When ImageMagick is not available the
     * method is a no-op returning false, so the upload passes through unchanged.
     *
     * @throws MimeTypeNotRegisteredException
     */
    public function convert(
        string $originalFile,
        string $setMimeType,
        string $expectedMimeType
    ): bool {
        $binary = $this->resolveImageMagickBinary('convert');
        if ($binary === null) {
            return false;
        }
        $sourceSuffix = $this->mimeTypeDetectorService->getFileExtensionsForMimeType($setMimeType)[0] ?? '';
        $sourceTemp = GeneralUtility::tempnam('mimeconverter_source_', '.' . $sourceSuffix);
        $targetTemp = GeneralUtility::tempnam('mimeconverter_target_', '.' . self::$forceConvertTypes[$expectedMimeType]);
        $output = [];
        $returnCode = 0;
        try {
            copy($originalFile, $sourceTemp);
            $command = $binary
                . ' ' . CommandUtility::escapeShellArgument($sourceTemp)
                . ' ' . CommandUtility::escapeShellArgument($targetTemp);
            CommandUtility::exec($command, $output, $returnCode);
            if ($returnCode !== 0 || !file_exists($targetTemp)) {
                return false;
            }
            copy($targetTemp, $originalFile);
        } finally {
            GeneralUtility::unlink_tempfile($sourceTemp);
            GeneralUtility::unlink_tempfile($targetTemp);
        }
        return true;
    }

    public function isBrokenMime(string $originalFile): bool
    {
        $binary = $this->resolveImageMagickBinary('identify');
        if ($binary === null) {
            // Without ImageMagick the state cannot be determined; do not report broken.
            return false;
        }
        $command = $binary . ' -regard-warnings ' . CommandUtility::escapeShellArgument($originalFile);
        $output = [];
        $returnCode = 0;
        CommandUtility::exec($command, $output, $returnCode);

        return (bool)$returnCode;
    }

    /**
     * Resolves the full command for an ImageMagick binary ("convert"/"identify") through the
     * configured GFX/binary paths, or null when it is not available. GraphicsMagick ships neither
     * "convert" nor "identify" as a standalone binary, so a null result also means the extension
     * stays inactive on a GraphicsMagick-only setup — which it cannot reliably serve anyway.
     */
    private function resolveImageMagickBinary(string $binary): ?string
    {
        $command = CommandUtility::getCommand($binary);

        return is_string($command) && $command !== '' ? $command : null;
    }
}
