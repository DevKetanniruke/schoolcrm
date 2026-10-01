<?php

namespace App\Livewire\Admin;

use App\Core\AI\Services\ManagementQueryEngine;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class ManagementIntelligence extends Component
{
    use AuthorizesRequests;

    public string $prompt = '';
    public ?array $activeResult = null;
    public array $queryHistory = [];

    public array $sampleQuestions = [
        "How many admission leads are pending follow-up?",
        "Which classes have attendance below the configured threshold?",
        "Show today's unresolved complaints.",
        "Summarize admission performance this month.",
        "Which follow-ups are overdue?",
    ];

    public function mount(): void
    {
        $this->runQuery("Summarize admission performance this month.");
    }

    public function runQuery(?string $customPrompt = null): void
    {
        $this->authorize('viewAny', User::class);

        $queryPrompt = $customPrompt ?: $this->prompt;

        if (empty(trim($queryPrompt))) {
            return;
        }

        $engine = app(ManagementQueryEngine::class);
        $user = Auth::user();

        $this->activeResult = $engine->executeQuery($queryPrompt, $user);
        $this->prompt = $queryPrompt;

        // Append to query history
        array_unshift($this->queryHistory, [
            'prompt' => $queryPrompt,
            'timestamp' => now()->format('h:i A'),
            'answer_snippet' => Str::limit(strip_tags($this->activeResult['answer'] ?? ''), 60),
        ]);

        if (count($this->queryHistory) > 10) {
            array_pop($this->queryHistory);
        }
    }

    public function render()
    {
        $this->authorize('viewAny', User::class);

        return view('livewire.admin.management-intelligence', [
            'sampleQuestions' => $this->sampleQuestions,
        ])->layout('components.layouts.admin', ['title' => 'Management Intelligence Dashboard']);
    }
}
