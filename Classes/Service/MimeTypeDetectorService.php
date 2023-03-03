<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Service;

use TYPO3\CMS\Core\Resource\MimeTypeDetector;
use WebVision\MimeConverter\Exception\InsufficientFileExtensionException;
use WebVision\MimeConverter\Exception\InsufficientMimeTypeException;
use WebVision\MimeConverter\Exception\MimeTypeNotRegisteredException;

class MimeTypeDetectorService
{
    protected MimeTypeDetector $t3MimeTypeDetector;

    public function __construct(MimeTypeDetector $mimeTypeDetector)
    {
        $this->t3MimeTypeDetector = $mimeTypeDetector;
    }

    /**
     * @return string[]
     * @throws MimeTypeNotRegisteredException
     */
    public function getFileExtensionsForMimeType(string $mimeType): array
    {
        $fileExt = $this->t3MimeTypeDetector->getFileExtensionsForMimeType($mimeType);
        if (empty($fileExt)) {
            throw new MimeTypeNotRegisteredException(
                sprintf('The mime type "%s" is not registered', $mimeType),
                1677575729573
            );
        }
        return $fileExt;
    }

    /**
     * @throws InsufficientMimeTypeException
     * @throws \InvalidArgumentException
     */
    public function detectMimeTypeFromFile(string $originalFile): string
    {
        if (!is_uploaded_file($originalFile) && !file_exists($originalFile)) {
            throw new \InvalidArgumentException(
                sprintf('File "%s" does not exist', $originalFile),
                1677576594374
            );
        }
        $mimeType = mime_content_type($originalFile);
        if (!$mimeType) {
            throw new InsufficientMimeTypeException(
                sprintf(
                    'Mime type for file "%s" could not be detected',
                    $originalFile
                ),
                1677590397667
            );
        }
        return $mimeType;
    }

    public function getExtensionFromFile(string $originalFile): string
    {
        return pathinfo($originalFile, PATHINFO_EXTENSION);
    }

    /**
     * @throws InsufficientFileExtensionException
     * @return string[]
     */
    public function getExpectedMimeTypeFromFileSuffix(string $fileSuffix): array
    {
        $expectedMimes = $this->t3MimeTypeDetector->getMimeTypesForFileExtension($fileSuffix);
        if (empty($expectedMimes)) {
            throw new InsufficientFileExtensionException(
                sprintf('File extension "%s" not registered with a mime type', $fileSuffix),
                1677590606903
            );
        }
        return $expectedMimes;
    }
}
