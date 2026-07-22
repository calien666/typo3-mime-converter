<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Report;

use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Reports\Status;
use TYPO3\CMS\Reports\StatusProviderInterface;
use WebVision\MimeConverter\Service\ImageMagickAvailabilityService;

/**
 * Reports whether ImageMagick - which this extension needs to convert files - is available,
 * surfacing a warning in the backend "Reports" module when it is missing. Only registered when
 * the optional EXT:reports is installed (see Configuration/Services.php).
 */
final class ImageMagickStatusReport implements StatusProviderInterface
{
    private const LLL = 'LLL:EXT:mime_converter/Resources/Private/Language/locallang.xlf:';

    public function __construct(
        private readonly ImageMagickAvailabilityService $availabilityService,
    ) {}

    public function getLabel(): string
    {
        return $this->translate('report.label', 'Mime Converter');
    }

    /**
     * @return Status[]
     */
    public function getStatus(): array
    {
        $available = $this->availabilityService->isAvailable();

        return [
            'imageMagick' => new Status(
                $this->translate('report.imageMagick.title', 'ImageMagick'),
                $available
                    ? $this->translate('report.imageMagick.available', 'Available')
                    : $this->translate('report.imageMagick.missing', 'Not available'),
                $available
                    ? ''
                    : $this->translate(
                        'report.imageMagick.missingMessage',
                        'ImageMagick was not found. Files are stored without mime type conversion.'
                    ),
                $available ? ContextualFeedbackSeverity::OK : ContextualFeedbackSeverity::WARNING
            ),
        ];
    }

    private function translate(string $key, string $default): string
    {
        return ($GLOBALS['LANG'] ?? null)?->sL(self::LLL . $key) ?: $default;
    }
}
