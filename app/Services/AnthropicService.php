<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AnthropicService
{
    private ?string $apiKey;

    public function __construct(SettingsService $settings)
    {
        $this->apiKey = $settings->get('anthropic_api_key');
    }

    public function generateSql(string $prompt, string $schemaContext): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'sql' => '',
                'error' => 'Anthropic API key not configured. Please add it in Settings.',
            ];
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-api-key' => $this->apiKey,
                    'anthropic-version' => '2023-06-01',
                ])
                ->post('https://api.anthropic.com/v1/messages', [
                    'model' => 'claude-sonnet-4-5-20250929',
                    'max_tokens' => 1024,
                    'system' => 'You are a ClickHouse SQL expert. Generate only the SQL query, no explanations or markdown formatting. Use ClickHouse-specific syntax. Do not wrap the query in code blocks.',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => "Given the following schema:\n\n{$schemaContext}\n\nGenerate a ClickHouse SQL query for: {$prompt}",
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $body = $response->json();
                $sql = trim($body['content'][0]['text'] ?? '');

                return [
                    'success' => true,
                    'sql' => $sql,
                    'error' => '',
                ];
            }

            $error = $response->json('error.message') ?? $response->body();

            return [
                'success' => false,
                'sql' => '',
                'error' => "Anthropic API error: {$error}",
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'sql' => '',
                'error' => "Request failed: {$e->getMessage()}",
            ];
        }
    }
}
