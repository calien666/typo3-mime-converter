<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Event;

use TYPO3\CMS\Core\Resource\Event\BeforeFileAddedEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebVision\MimeConverter\Converter\AbstractFileConverter;
use WebVision\MimeConverter\Converter\FileConverterRegistry;
use WebVision\MimeConverter\Exception\InsufficientFileExtensionException;
use WebVision\MimeConverter\Exception\InsufficientMimeTypeException;
use WebVision\MimeConverter\Exception\MimeTypeNotRegisteredException;
use WebVision\MimeConverter\Service\MimeTypeDetectorService;

/**
 * Handles the BeforeFileAddEvent in ResourceStorage
 * @internal
 * This class is internal use in mime_converter and no public API
 */
final class ConvertFileToCorrectMimeType
{
    protected MimeTypeDetectorService $mimeTypeDetectorService;

    public function __construct(MimeTypeDetectorService $mimeTypeDetectorService)
    {
        $this->mimeTypeDetectorService = $mimeTypeDetectorService;
    }

    public function __invoke(BeforeFileAddedEvent $beforeFileAddedEvent): void
    {
        $originalFile = $beforeFileAddedEvent->getSourceFilePath();
        $targetFileName = $beforeFileAddedEvent->getFileName();
        // detect mime type from upload
        try {
            $mimeType = $this->mimeTypeDetectorService
                ->detectMimeTypeFromFile($originalFile);
        } catch (InsufficientMimeTypeException $e) {
            return;
        }
        // detect file suffix from target file name
        $fileSuffixFromFileName = $this->mimeTypeDetectorService
            ->getExtensionFromFile($targetFileName);
        //detect expected suffix from mime type
        try {
            $expectedFileSuffix = $this->mimeTypeDetectorService
                ->getFileExtensionsForMimeType($mimeType);
        } catch (MimeTypeNotRegisteredException $e) {
            /**
             * should NEVER run inside, as $mimeType is detected above
             */
            return;
        }
        // detect mime type associated with file suffix
        // returns if empty array cause of Exception
        // so no check for array key 0 is needed in next step
        try {
            $expectedMimeType = $this->mimeTypeDetectorService
                ->getExpectedMimeTypeFromFileSuffix($fileSuffixFromFileName);
        } catch (InsufficientFileExtensionException $e) {
            return;
        }

        if (
            !in_array($fileSuffixFromFileName, $expectedFileSuffix)
            && !in_array($mimeType, $expectedMimeType)
        ) {
            // array key 0 is set, see last exception catch
            $provider = GeneralUtility::makeInstance(FileConverterRegistry::class)
                ->findConverterForMimeType($mimeType, $expectedMimeType[0]);
            if ($provider instanceof AbstractFileConverter) {
                try {
                    //$copiedFile = $provider->copyFile($originalFile, $expectedFileSuffix[0]);
                    $provider->convert($originalFile, $mimeType, $expectedMimeType[0]);
                } catch (MimeTypeNotRegisteredException $e) {
                    // TODO: Possibly log here
                }
            }
        }
    }
}
