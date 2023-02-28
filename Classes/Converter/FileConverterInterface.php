<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Converter;

interface FileConverterInterface
{
    public static function canConvert(string $mimeType, string $expectedMimeType): bool;

    public function convert(string $originalFile, string $setMimeType, string $expectedMimeType): bool;
}
