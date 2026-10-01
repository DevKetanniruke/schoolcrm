<?php

namespace App\Core\AI\Providers;

use App\Core\AI\Contracts\AIProviderInterface;
use App\Core\AI\DTOs\AIResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIProvider implements AIProviderInterface
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('ai.providers.openai.api_key', '');
        $this->model = config('ai.providers.openai.model', 'gpt-4o-mini');
    }

    public function getProviderName(): string
    {
        return 'openai';
    }

    public function generateCompletion(string $prompt, array $options = []): AIResponse
    {
        if (empty($this->apiKey)) {
            // Fallback to local driver if API key is not configured
            Log::warning("OpenAI API key missing, falling back to LogLocalAiProvider");
            $fallback = new LogLocalAiProvider();
            return $fallback->generateCompletion($prompt, $options);
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $options['system_prompt'] ?? 'You are a helpful School CRM AI assistant.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';
                $promptTokens = $data['usage']['prompt_tokens'] ?? 0;
                $completionTokens = $data['usage']['completion_tokens'] ?? 0;

                return AIResponse::makeSuccess($content, $promptTokens, $completionTokens);
            }

            return AIResponse::makeError("OpenAI API call failed: " . $response->body());
        } catch (\Throwable $e) {
            Log::error("OpenAI Provider Error: " . $e->getMessage());
            return AIResponse::makeError($e->getMessage());
        }
    }
}
