<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Converter;

use Symfony\Component\DependencyInjection\ServiceLocator;
use TYPO3\CMS\Core\SingletonInterface;

final class FileConverterRegistry implements SingletonInterface
{
    /**
     * @param ServiceLocator<AbstractFileConverter> $mimeConverterLocator
     */
    public function __construct(
        private readonly ServiceLocator $mimeConverterLocator
    ) {}

    public function findConverterForMimeType(
        string $mimeType,
        string $expectedMimeType
    ): ?AbstractFileConverter {
        /** @var class-string<AbstractFileConverter> $converter */
        foreach ($this->mimeConverterLocator->getProvidedServices() as $identifier => $converter) {
            if ($converter::canConvert($mimeType, $expectedMimeType)) {
                return $this->mimeConverterLocator->get($identifier);
            }
        }
        return null;
    }
}
