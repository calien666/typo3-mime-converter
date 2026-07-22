<?php

declare(strict_types=1);

namespace WebVision\MimeConverter\Tests\Functional\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use WebVision\MimeConverter\Service\ImageMagickAvailabilityService;

#[CoversClass(ImageMagickAvailabilityService::class)]
final class ImageMagickAvailabilityServiceTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/mime_converter/',
    ];

    #[Test]
    public function imageMagickIsAvailableInTheTestEnvironment(): void
    {
        $subject = $this->get(ImageMagickAvailabilityService::class);
        $this->assertInstanceOf(ImageMagickAvailabilityService::class, $subject);

        $this->assertTrue($subject->isAvailable());
    }

    #[Test]
    public function resolveBinaryReturnsAPathForConvertAndNullForAMissingBinary(): void
    {
        $subject = $this->get(ImageMagickAvailabilityService::class);
        $this->assertInstanceOf(ImageMagickAvailabilityService::class, $subject);

        $this->assertNotNull($subject->resolveBinary('convert'));
        $this->assertNull($subject->resolveBinary('this-binary-does-not-exist-xyz'));
    }
}
