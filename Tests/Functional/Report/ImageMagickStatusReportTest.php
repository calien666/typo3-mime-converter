<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Tests\Functional\Report;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use WebVision\MimeConverter\Report\ImageMagickStatusReport;

#[CoversClass(ImageMagickStatusReport::class)]
final class ImageMagickStatusReportTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = [
        'reports',
    ];

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/mime_converter/',
    ];

    #[Test]
    public function statusReportsImageMagickAsAvailable(): void
    {
        $subject = $this->get(ImageMagickStatusReport::class);
        $this->assertInstanceOf(ImageMagickStatusReport::class, $subject);
        $this->assertNotSame('', $subject->getLabel());

        $status = $subject->getStatus();

        $this->assertArrayHasKey('imageMagick', $status);
        $this->assertSame(
            ContextualFeedbackSeverity::OK,
            $status['imageMagick']->getSeverity()
        );
    }
}
