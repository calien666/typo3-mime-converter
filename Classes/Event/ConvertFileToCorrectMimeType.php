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
use WebVision\MimeConverter\Utility\MimeTypeUtility;

/**
 * @internal
 */
final class ConvertFileToCorrectMimeType
{
    public function __invoke(BeforeFileAddedEvent $beforeFileAddedEvent): void
    {
        $originalFile = $beforeFileAddedEvent->getSourceFilePath();
        $targetFileName = $beforeFileAddedEvent->getFileName();
        // detect mime type from upload
        try {
            $mimeType = MimeTypeUtility::detectMimeTypeFromFile($originalFile);
        } catch (InsufficientMimeTypeException $e) {
            return;
        }
        // detect file suffix from target file name
        $fileSuffixFromFileName = MimeTypeUtility::getFileExtensionFromFile($targetFileName);
        //detect expected suffix from mime type
        try {
            $expectedFileSuffix = MimeTypeUtility::detectFileExtensionFromMimeType($mimeType);
        } catch (MimeTypeNotRegisteredException $e) {
            /**
             * should NEVER run inside, as $mimeType is detected above
             */
            return;
        }
        // detect mime type associated with file suffix
        try {
            $expectedMimeType = MimeTypeUtility::getExpectedMimeTypeFromFileSuffix($fileSuffixFromFileName);
        } catch (InsufficientFileExtensionException $e) {
            return;
        }

        if (
            !in_array($fileSuffixFromFileName, $expectedFileSuffix)
            && $mimeType !== $expectedMimeType
        ) {
            $provider = GeneralUtility::makeInstance(FileConverterRegistry::class)
                ->findConverterForMimeType($mimeType, $expectedMimeType);
            if ($provider instanceof AbstractFileConverter) {
                $provider->convert($originalFile, $mimeType, $expectedMimeType);
            }
        }
    }
}
