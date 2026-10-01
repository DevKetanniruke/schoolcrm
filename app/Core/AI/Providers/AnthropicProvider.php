<?php

namespace App\Core\AI\Providers;

use App\Core\AI\Contracts\AIProviderInterface;
use App\Core\AI\DTOs\AIResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicProvider implements AIProviderInterface
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('ai.providers.anthropic.api_key', '');
        $this->model = config('ai.providers.anthropic.model', 'claude-3-5-sonnet-20241022');
    }

    public function getProviderName(): string
    {
        return 'anthropic';
    }

    public function generateCompletion(string $prompt, array $options = []): AIResponse
    {
        if (empty($this->apiKey)) {
            Log::warning("Anthropic API key missing, falling back to LogLocalAiProvider");
            $fallback = new LogLocalAiProvider();
            return $fallback->generateCompletion($prompt, $options);
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => $this->model,
                'max_tokens' => 1024,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['content'][0]['text'] ?? '';
                $promptTokens = $data['usage']['input_tokens'] ?? 0;
                $completionTokens = $data['usage']['output_tokens'] ?? 0;

                return AIResponse::makeSuccess($content, $promptTokens, $completionTokens);
            }

            return AIResponse::makeError("Anthropic API call failed: " . $response->body());
        } catch (\Throwable $e) {
            Log::error("Anthropic Provider Error: " . $e->getMessage());
            return AIResponse::makeError($e->getMessage());
        }
    }
}
