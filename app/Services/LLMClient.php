<?php

namespace App\Services;

namespace App\Services;

use GuzzleHttp\Client;

class LLMClient
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('services.llm.base_uri'),
            'headers' => [
                'Authorization' => 'Bearer '.config('services.llm.api_key'),
            ],
        ]);
    }

    public function call(string $prompt, array $options = []): string
    {
        $response = $this->client->post('/v1/complete', [
            'json' => [
                'model' => $options['model'] ?? 'claude-2',
                'prompt' => $prompt,
                'max_tokens' => $options['max_tokens'] ?? 256,
            ],
        ]);

        return json_decode((string) $response->getBody(), true)['completion'] ?? '';
    }
}
