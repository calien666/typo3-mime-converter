<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Converter\Provider;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebVision\MimeConverter\Converter\AbstractFileConverter;
use WebVision\MimeConverter\Exception\MimeTypeNotRegisteredException;
use WebVision\MimeConverter\Service\ImageMagickAvailabilityService;

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
final class ImageConverterProvider extends AbstractFileConverter implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected ImageMagickAvailabilityService $availabilityService;
    protected FlashMessageService $flashMessageService;

    public function injectImageMagickAvailabilityService(ImageMagickAvailabilityService $availabilityService): void
    {
        $this->availabilityService = $availabilityService;
    }

    public function injectFlashMessageService(FlashMessageService $flashMessageService): void
    {
        $this->flashMessageService = $flashMessageService;
    }

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
        $binary = $this->availabilityService->resolveBinary('convert');
        if ($binary === null) {
            $this->reportMissingImageMagick();
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
        $binary = $this->availabilityService->resolveBinary('identify');
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
     * A file needed conversion but ImageMagick is missing: log it and, in a backend upload,
     * surface a flash message so the editor knows the file was stored without conversion.
     */
    private function reportMissingImageMagick(): void
    {
        $this->logger?->warning(
            'ImageMagick is not available; the uploaded file was stored without mime type conversion.'
        );

        $languageService = $GLOBALS['LANG'] ?? null;
        $file = 'LLL:EXT:mime_converter/Resources/Private/Language/locallang.xlf:';
        $title = $languageService?->sL($file . 'missingImageMagick.title') ?: 'Mime conversion skipped';
        $body = $languageService?->sL($file . 'missingImageMagick.message')
            ?: 'ImageMagick was not found on the server. The uploaded file was stored without mime type conversion.';

        $this->flashMessageService
            ->getMessageQueueByIdentifier()
            ->enqueue(new FlashMessage($body, $title, ContextualFeedbackSeverity::WARNING, true));
    }
}
