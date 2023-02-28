<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Converter;

abstract class AbstractFileConverter implements FileConverterInterface
{
    protected int $priority = 0;

    public function getPriority(): int
    {
        return $this->priority;
    }
}
