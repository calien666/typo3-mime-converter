<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Reports\StatusProviderInterface;
use WebVision\MimeConverter\Report\ImageMagickStatusReport;

return static function (ContainerConfigurator $configurator): void {
    // The status report implements an EXT:reports interface, so it may only be registered
    // (and thus loaded) when that optional extension is installed.
    if (interface_exists(StatusProviderInterface::class)) {
        $configurator->services()
            ->set(ImageMagickStatusReport::class)
            ->autowire()
            ->autoconfigure();
    }
};
