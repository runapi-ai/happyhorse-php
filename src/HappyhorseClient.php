<?php

declare(strict_types=1);

namespace RunApi\Happyhorse;

use RunApi\Core\BaseClient;
use RunApi\Core\ClientOptions;
use RunApi\Happyhorse\Resources\EditVideo;
use RunApi\Happyhorse\Resources\ImageToVideo;
use RunApi\Happyhorse\Resources\TextToVideo;

/**
 * Provides HappyHorse video generation and editing.
 *
 * Exposes typed model resources plus the universal files and account resources.
 */
final class HappyhorseClient extends BaseClient
{
    /**
     * Text to video operations.
     */
    public readonly TextToVideo $textToVideo;
    /**
     * Image to video operations.
     */
    public readonly ImageToVideo $imageToVideo;
    /**
     * Edit video operations.
     */
    public readonly EditVideo $editVideo;

    /**
     * Create a HappyHorse client with optional API key, base URL, and transport overrides.
     */
    public function __construct(ClientOptions $options = new ClientOptions())
    {
        parent::__construct($options);
        $this->textToVideo = TextToVideo::fromHttp($this->http);
        $this->imageToVideo = ImageToVideo::fromHttp($this->http);
        $this->editVideo = EditVideo::fromHttp($this->http);
    }
}
