<?php

declare(strict_types=1);

namespace RunApi\Happyhorse\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RunApi\Core\ClientOptions;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;
use RunApi\Happyhorse\HappyhorseClient;
use RunApi\Happyhorse\Models\CompletedVideoTaskResponse;
use RunApi\Happyhorse\Resources\EditVideo;
use RunApi\Happyhorse\Resources\ImageToVideo;
use RunApi\Happyhorse\Resources\TextToVideo;

final class HappyhorseClientTest extends TestCase
{
    public function testExposesTypedResources(): void
    {
        $client = new HappyhorseClient(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        self::assertInstanceOf(TextToVideo::class, $client->textToVideo);
        self::assertInstanceOf(ImageToVideo::class, $client->imageToVideo);
        self::assertInstanceOf(EditVideo::class, $client->editVideo);
    }

    public function testCreatePostsCompactedBodyToCorrectPath(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
        ]);
        $client = new HappyhorseClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $task = $client->textToVideo->create([
            'model' => 'happyhorse-character',
            'aspect_ratio' => '16:9',
            'output_resolution' => '720p',
            'prompt' => 'A product render',
            'reference_image_urls' => ['https://cdn.runapi.ai/public/samples/image.jpg'],
            'callback_url' => '',
            'seed' => null,
        ]);

        $body = json_decode((string) $transport->requests[0]->getBody(), true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('task_1', $task->id);
        self::assertSame('/api/v1/happyhorse/text_to_video', $transport->requests[0]->getUri()->getPath());
        self::assertSame('happyhorse-character', $body['model']);
        self::assertArrayNotHasKey('callback_url', $body);
        self::assertArrayNotHasKey('seed', $body);
    }

    public function testRunReturnsTypedCompletedResponseAndPreservesUnknownFields(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
            new Response(200, [], '{"id":"task_1","status":"completed","videos":[{"url":"https://file.runapi.ai/result"}],"extra_field":"kept"}'),
        ]);
        $client = new HappyhorseClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $result = $client->textToVideo->run([
            'model' => 'happyhorse-character',
            'aspect_ratio' => '16:9',
            'output_resolution' => '720p',
            'prompt' => 'A product render',
            'reference_image_urls' => ['https://cdn.runapi.ai/public/samples/image.jpg'],
        ]);

        self::assertInstanceOf(CompletedVideoTaskResponse::class, $result);
        self::assertSame('https://file.runapi.ai/result', $result->videos[0]->url);
        self::assertSame('kept', $result->toArray()['extra_field']);
        self::assertSame('/api/v1/happyhorse/text_to_video/task_1', $transport->requests[1]->getUri()->getPath());
    }

    public function testCompletedResponseRequiresResultFiles(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
            new Response(200, [], '{"id":"task_1","status":"completed"}'),
        ]);
        $client = new HappyhorseClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('videos is required');

        $client->textToVideo->run([
            'model' => 'happyhorse-character',
            'aspect_ratio' => '16:9',
            'output_resolution' => '720p',
            'prompt' => 'A product render',
            'reference_image_urls' => ['https://cdn.runapi.ai/public/samples/image.jpg'],
        ]);
    }

    public function testRejectsInvalidContractEnum(): void
    {
        $client = new HappyhorseClient(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('aspect_ratio must be one of the allowed values');

        $client->textToVideo->create([
        'model' => 'happyhorse-character',
        'output_resolution' => '720p',
        'prompt' => 'A product render',
        'reference_image_urls' => ['https://cdn.runapi.ai/public/samples/image.jpg'],
        'aspect_ratio' => 'not-valid',
        ]);
    }

    public function testSecondaryResourceUsesItsOwnPath(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_2"}'),
        ]);
        $client = new HappyhorseClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $client->imageToVideo->create([
            'model' => 'happyhorse-image-to-video',
            'first_frame_image_url' => 'https://cdn.runapi.ai/public/samples/image.jpg',
            'output_resolution' => '720p',
            'prompt' => 'A product render',
        ]);

        self::assertSame('/api/v1/happyhorse/image_to_video', $transport->requests[0]->getUri()->getPath());
    }
}
