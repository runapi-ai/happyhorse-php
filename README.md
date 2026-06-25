# HappyHorse PHP SDK for RunAPI

[![Packagist](https://img.shields.io/packagist/v/runapi-ai/happyhorse)](https://packagist.org/packages/runapi-ai/happyhorse)
[![License](https://img.shields.io/github/license/runapi-ai/happyhorse-php)](https://github.com/runapi-ai/happyhorse-php/blob/main/LICENSE)

The HappyHorse PHP SDK is the Composer package for HappyHorse on RunAPI. Use it when your PHP application needs associative-array request bodies, task status lookup, polling helpers, file helpers, and consistent RunAPI errors.

## Install

```bash
composer require runapi-ai/happyhorse
```

## Quick start

```php
<?php

require __DIR__ . "/vendor/autoload.php";

use RunApi\Happyhorse\HappyhorseClient;

$client = new HappyhorseClient(); // reads RUNAPI_API_KEY

$task = $client->textToVideo->create([
    'model' => 'happyhorse-text-to-video',
    'prompt' => 'A precise product render on white marble',
]);

$status = $client->textToVideo->get($task->id);

$result = $client->textToVideo->run([
    'model' => 'happyhorse-text-to-video',
    'prompt' => 'A serene mountain lake at dawn',
]);

echo $result->videos[0]->url . PHP_EOL;
```

Use `create()` to submit a task and return quickly, `get()` to fetch the latest task state, and `run()` when a script should create and poll until completion. In web request handlers, prefer `create()` plus webhook or later `get()` polling so a worker is not held open.

Returned file URLs are temporary. Download and store generated files in your own durable storage within the retention window.

All SDK exceptions inherit from `RunApi\Core\Errors\RunApiException`, including validation, authentication, rate limit, task failure, and task timeout errors.

## Links

- Model page: https://runapi.ai/models/happyhorse
- SDK docs: https://runapi.ai/docs#sdk-happyhorse
- Product docs: https://runapi.ai/docs#happyhorse
- Pricing and rate limits: https://runapi.ai/models/happyhorse/edit-video
- Full catalog: https://runapi.ai/models
- GitHub repository: https://github.com/runapi-ai/happyhorse-php
- Multi-language SDK repository: https://github.com/runapi-ai/happyhorse-sdk

## License

Licensed under the Apache License, Version 2.0.
