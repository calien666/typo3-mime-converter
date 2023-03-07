<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Converter;

use WebVision\MimeConverter\Exception\MimeTypeNotRegisteredException;

interface FileConverterInterface
{
    public static function canConvert(string $mimeType, string $expectedMimeType): bool;

    /**
     * @throws MimeTypeNotRegisteredException
     */
    public function convert(string $originalFile, string $setMimeType, string $expectedMimeType): bool;

    public function copyFile(string $originalFile, string $fileSuffix): ?string;

    public function isBrokenMime(string $originalFile): bool;
}
