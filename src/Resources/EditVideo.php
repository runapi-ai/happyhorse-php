<?php

declare(strict_types=1);

namespace RunApi\Happyhorse\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\RequestOptions;
use RunApi\Core\Resources\TypedConfiguredResource;
use RunApi\Happyhorse\Models\CompletedVideoTaskResponse;
use RunApi\Happyhorse\Models\VideoTaskResponse;
use RunApi\Happyhorse\Types;

/**
 * Transforms an existing video with a text prompt and optional reference images. Use AudioSetting to control whether original audio is preserved.
 */
readonly class EditVideo extends TypedConfiguredResource
{
    /**
     * Submits an edit-video task and returns immediately with a task id.
     *
     * @param array{
     *   model: string,
     *   prompt: string,
     *   source_video_url: string,
     *   audio_setting?: string,
     *   callback_url?: string,
     *   output_resolution?: string,
     *   reference_image_urls?: list<string>
     * } $params
     */
    public function create(array $params, ?RequestOptions $options = null): TaskCreateResponse
    {
        return parent::create($params, $options);
    }

    /**
     * Fetches the current status of an edit-video task by id.
     */
    public function get(string $id, ?RequestOptions $options = null): VideoTaskResponse
    {
        $response = parent::get($id, $options);

        /** @var VideoTaskResponse $response */
        return $response;
    }

    /**
     * Submits an edit-video task and polls until it completes.
     *
     * @param array{
     *   model: string,
     *   prompt: string,
     *   source_video_url: string,
     *   audio_setting?: string,
     *   callback_url?: string,
     *   output_resolution?: string,
     *   reference_image_urls?: list<string>
     * } $params
     */
    public function run(array $params, ?RequestOptions $options = null): CompletedVideoTaskResponse
    {
        $response = parent::run($params, $options);

        /** @var CompletedVideoTaskResponse $response */
        return $response;
    }

    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self(
            $http,
            '/api/v1/happyhorse/edit_video',
            'happyhorse/edit-video',
            VideoTaskResponse::class,
            CompletedVideoTaskResponse::class,
            Types::EDIT_VIDEO_MODELS,
            'edit-video',
            VideoTaskResponse::class,
            CompletedVideoTaskResponse::class,
        );
    }
}
