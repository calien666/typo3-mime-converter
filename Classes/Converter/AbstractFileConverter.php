<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Converter;

use WebVision\MimeConverter\Service\MimeTypeDetectorService;

/**
 * This abstract class is needed for autowiring and autoloading
 * all converters. @see Configuration/Services.yaml
 * For example converter @see \WebVision\MimeConverter\Converter\Provider\ImageConverterProvider
 */
abstract class AbstractFileConverter implements FileConverterInterface
{
    protected MimeTypeDetectorService $mimeTypeDetectorService;

    public function __construct(
        MimeTypeDetectorService $mimeTypeDetectorService
    ) {
        $this->mimeTypeDetectorService = $mimeTypeDetectorService;
    }

    public function copyFile(string $originalFile, string $fileSuffix): ?string
    {
        if (is_uploaded_file($originalFile)) {
            return null;
        }
        $setFileSuffix = $this->mimeTypeDetectorService->getExtensionFromFile($originalFile);
        $regex = sprintf('/\.(%s)$/', $setFileSuffix);
        $replace = sprintf('.%s', $fileSuffix);
        // use preg_replace to ensure. ONLY fileSuffix is replaced
        $newFileName = preg_replace($regex, $replace, $originalFile, 1);
        copy($originalFile, $newFileName);
        return $newFileName;
    }
}
