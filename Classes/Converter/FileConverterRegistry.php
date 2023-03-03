<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Converter;

use Symfony\Component\DependencyInjection\ServiceLocator;
use TYPO3\CMS\Core\SingletonInterface;

final class FileConverterRegistry implements SingletonInterface
{
    public function __construct(
        private readonly ServiceLocator $mimeConverterLocator
    ) {
    }

    public function findConverterForMimeType(
        string $mimeType,
        string $expectedMimeType
    ): ?AbstractFileConverter {
        /** @var class-string|FileConverterInterface $converter */
        foreach ($this->mimeConverterLocator->getProvidedServices() as $identifier => $converter) {
            if ($converter::canConvert($mimeType, $expectedMimeType)) {
                /** @var AbstractFileConverter $provider */
                $provider = $this->mimeConverterLocator->get($identifier);
                return $provider;
            }
        }
        return null;
    }
}
