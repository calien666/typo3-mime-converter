<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Service;

use TYPO3\CMS\Core\Utility\CommandUtility;

/**
 * Resolves the ImageMagick command line binaries the extension relies on.
 *
 * GraphicsMagick ships neither "convert" nor "identify" as a standalone binary, so a null
 * result also signals a GraphicsMagick-only setup, which this extension cannot reliably serve.
 */
final class ImageMagickAvailabilityService
{
    /**
     * Full command for an ImageMagick binary ("convert"/"identify"), resolved through the
     * configured GFX/binary paths, or null when it is not available on the system.
     */
    public function resolveBinary(string $binary): ?string
    {
        $command = CommandUtility::getCommand($binary);

        return is_string($command) && $command !== '' ? $command : null;
    }

    public function isAvailable(): bool
    {
        return $this->resolveBinary('convert') !== null
            && $this->resolveBinary('identify') !== null;
    }
}
