<?php

declare(strict_types=1);

namespace RunApi\Happyhorse;

/**
 * Constants for model slugs supported by the HappyHorse PHP SDK.
 */
final class Types
{
    /** @var list<string> */
    public const TEXT_TO_VIDEO_MODELS = ['happyhorse-character', 'happyhorse-text-to-video'];

    /** @var list<string> */
    public const IMAGE_TO_VIDEO_MODELS = ['happyhorse-image-to-video'];

    /** @var list<string> */
    public const EDIT_VIDEO_MODELS = ['happyhorse-edit-video'];

    private function __construct()
    {
    }
}
