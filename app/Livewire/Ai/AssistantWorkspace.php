<?php

namespace App\Livewire\Ai;

use App\Core\AI\Services\AIService;
use App\Models\AdmissionLead;
use App\Models\AIUsageLog;
use App\Models\ParentProfile;
use App\Models\Student;
use Carbon\Carbon;
use Livewire\Component;

class AssistantWorkspace extends Component
{
    public string $selectedCapability = 'school_assistant';
    public string $prompt = '';
    public string $preferredDriver = 'local_heuristic';

    // Entity Binders
    public ?int $selectedStudentId = null;
    public ?int $selectedLeadId = null;
    public ?int $selectedParentId = null;

    // Chat History Stream
    public array $chatMessages = [];

    protected $rules = [
        'prompt' => 'required|string|min:3',
        'selectedCapability' => 'required|string',
    ];

    public function mount()
    {
        $this->preferredDriver = config('ai.default', 'local_heuristic');
        
        // Initial welcome message
        $this->chatMessages[] = [
            'role' => 'assistant',
            'capability' => 'school_assistant',
            'content' => "👋 **Welcome to SmartSchool AI Copilot!**\n\nI am your intelligent assistant layer. Select a capability tab above or use one of the quick prompt suggestions below to analyze student records, draft communications, summarize overdue follow-ups, or query school health metrics.",
            'timestamp' => now()->format('H:i'),
            'suggestedActions' => [],
        ];
    }

    public function setCapability(string $capability)
    {
        $this->selectedCapability = $capability;
    }

    public function usePresetPrompt(string $preset)
    {
        $this->prompt = $preset;
    }

    public function submitPrompt(AIService $aiService)
    {
        $this->validate();

        $user = auth()->user();
        $userPrompt = $this->prompt;

        // Add user message to chat stream
        $this->chatMessages[] = [
            'role' => 'user',
            'capability' => $this->selectedCapability,
            'content' => $userPrompt,
            'timestamp' => now()->format('H:i'),
        ];

        $this->prompt = '';

        // Entity IDs payload
        $entityIds = [
            'student_id' => $this->selectedStudentId,
            'lead_id' => $this->selectedLeadId,
            'parent_id' => $this->selectedParentId,
        ];

        // Execute AI Capability
        $response = $aiService->executeCapability(
            $this->selectedCapability,
            $userPrompt,
            $user,
            $entityIds,
            $this->preferredDriver
        );

        // Add AI assistant response to chat stream
        $this->chatMessages[] = [
            'role' => 'assistant',
            'capability' => $this->selectedCapability,
            'content' => $response->content,
            'success' => $response->success,
            'timestamp' => now()->format('H:i'),
            'suggestedActions' => $response->suggestedActions,
            'tokens' => $response->totalTokens,
        ];
    }

    public function clearChat()
    {
        $this->chatMessages = [];
        $this->mount();
    }

    public function render()
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? 1;

        // Daily usage stats
        $dailyRequests = AIUsageLog::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $dailyTokens = AIUsageLog::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->sum('total_tokens');

        $dailyLimit = (int) config('ai.limits.daily_requests_per_user', 100);

        // Dynamic Entity Options (permission checked)
        $students = $user->hasPermissionTo('students.view') 
            ? Student::where('school_id', $schoolId)->orderBy('first_name')->take(20)->get() 
            : collect();

        $leads = $user->hasPermissionTo('admissions.view') 
            ? AdmissionLead::where('school_id', $schoolId)->orderBy('parent_name')->take(20)->get() 
            : collect();

        $parents = $user->hasPermissionTo('parents.view') 
            ? ParentProfile::where('school_id', $schoolId)->orderBy('first_name')->take(20)->get() 
            : collect();

        $capabilitiesMap = [
            'school_assistant' => 'AI School Assistant',
            'admission_lead_assistant' => 'Admission Lead Assistant',
            'communication_generator' => 'Communication Generator',
            'followup_assistant' => 'Follow-up Assistant',
            'student_summary_generator' => 'Student Summary Generator',
            'parent_communication_assistant' => 'Parent Communication Assistant',
            'management_query_assistant' => 'Management Query Assistant',
        ];

        return view('livewire.ai.assistant-workspace', [
            'dailyRequests' => $dailyRequests,
            'dailyTokens' => $dailyTokens,
            'dailyLimit' => $dailyLimit,
            'students' => $students,
            'leads' => $leads,
            'parents' => $parents,
            'capabilitiesMap' => $capabilitiesMap,
        ])->layout('components.layouts.admin', ['title' => 'AI Copilot Assistant']);
    }
}
