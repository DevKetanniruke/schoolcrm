<?php

namespace App\Core\AI\Services;

use App\Core\AI\Contracts\AIProviderInterface;
use App\Core\AI\DTOs\AIResponse;
use App\Core\AI\Providers\AnthropicProvider;
use App\Core\AI\Providers\LogLocalAiProvider;
use App\Core\AI\Providers\OpenAIProvider;
use App\Models\AdmissionLead;
use App\Models\AIUsageLog;
use App\Models\CrmTask;
use App\Models\ParentProfile;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Supported AI capabilities and required RBAC permission slugs.
     */
    protected array $capabilityPermissions = [
        'school_assistant' => null, // Available to all authenticated CRM users
        'admission_lead_assistant' => 'admissions.view',
        'lead_history_summary' => 'admissions.view',
        'lead_followup_suggestions' => 'admissions.view',
        'draft_personalized_message' => 'admissions.view',
        'overdue_followups_identifier' => 'admissions.view',
        'enquiry_conversation_summary' => 'admissions.view',
        'counselor_call_notes' => 'admissions.view',
        'visit_summaries' => 'admissions.view',
        'next_admin_actions' => 'admissions.view',
        'communication_generator' => 'communications.view',
        'parent_communication_generator' => 'communications.send',
        'followup_assistant' => 'tasks.view',
        'student_summary_generator' => 'students.view',
        'parent_communication_assistant' => 'parents.view',
        'management_query_assistant' => 'users.view',
    ];

    /**
     * Resolve the active AI provider driver.
     */
    public function resolveProvider(?string $driver = null): AIProviderInterface
    {
        $driver = $driver ?? config('ai.default', 'local_heuristic');

        return match ($driver) {
            'openai' => new OpenAIProvider(),
            'anthropic' => new AnthropicProvider(),
            default => new LogLocalAiProvider(),
        };
    }

    /**
     * Dispatch an AI capability prompt request while enforcing RBAC security & rate limits.
     */
    public function executeCapability(
        string $capability,
        string $prompt,
        User $user,
        array $entityIds = [],
        ?string $preferredDriver = null
    ): AIResponse {
        // 1. Validate authorization
        if (! $this->userIsAuthorized($user, $capability)) {
            return AIResponse::makeError("Access Denied: You do not have permission to access capability '{$capability}' or associated entity data.");
        }

        // 2. Enforce Daily Token & Request Rate Limit
        if ($this->hasExceededDailyLimit($user)) {
            return AIResponse::makeError("Usage Limit Exceeded: You have reached your daily AI assistant request quota (" . config('ai.limits.daily_requests_per_user', 100) . " requests/day).");
        }

        // 3. Hydrate permission-safe entity context
        $context = $this->hydrateContext($capability, $user, $entityIds);

        // 4. Resolve provider driver
        $provider = $this->resolveProvider($preferredDriver);

        // 5. Generate completion
        $response = $provider->generateCompletion($prompt, [
            'capability' => $capability,
            'context' => $context,
            'locale' => app()->getLocale(),
            'user_id' => $user->id,
            'school_id' => $user->school_id ?? 1,
        ]);

        // 6. Log AI usage audit trail
        if ($response->success) {
            $this->logUsage($user, $provider->getProviderName(), $capability, $response);
        }

        return $response;
    }

    /**
     * Check if user has permission for the specified capability.
     */
    public function userIsAuthorized(User $user, string $capability): bool
    {
        $requiredPermission = $this->capabilityPermissions[$capability] ?? null;

        if (! $requiredPermission) {
            return true;
        }

        return $user->hasPermissionTo($requiredPermission);
    }

    /**
     * Check daily request limit for current user.
     */
    public function hasExceededDailyLimit(User $user): bool
    {
        $dailyLimit = (int) config('ai.limits.daily_requests_per_user', 100);

        $todayCount = AIUsageLog::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        return $todayCount >= $dailyLimit;
    }

    /**
     * Hydrate contextual entity information securely based on entity IDs and permissions.
     */
    protected function hydrateContext(string $capability, User $user, array $entityIds): array
    {
        $schoolId = $user->school_id ?? 1;
        $context = ['school_id' => $schoolId];

        // Check if capability belongs to Admission Assistant domain
        $isAdmissionCapability = str_starts_with($capability, 'lead_') 
            || str_starts_with($capability, 'draft_')
            || str_starts_with($capability, 'overdue_')
            || str_starts_with($capability, 'enquiry_')
            || str_starts_with($capability, 'counselor_')
            || str_starts_with($capability, 'visit_')
            || str_starts_with($capability, 'next_')
            || $capability === 'admission_lead_assistant';

        if ($isAdmissionCapability && ! empty($entityIds['lead_id']) && $user->hasPermissionTo('admissions.view')) {
            $lead = AdmissionLead::with(['enquiries', 'followUps', 'documents', 'application', 'statusHistories'])
                ->where('school_id', $schoolId)
                ->find($entityIds['lead_id']);

            if ($lead) {
                $context['lead_id'] = $lead->id;
                $context['parent_name'] = $lead->parent_name;
                $context['student_name'] = $lead->student_name;
                $context['stage'] = $lead->stage;
                $context['status'] = $lead->status;
                $context['phone'] = $lead->phone;
                $context['email'] = $lead->email;
                $context['enquiries_count'] = $lead->enquiries->count();
                $context['followups_count'] = $lead->followUps->count();
                $context['overdue_followups_count'] = $lead->followUps->where('status', 'pending')->where('due_date', '<', now())->count();
                $context['documents_count'] = $lead->documents->count();
                $context['application_number'] = $lead->application?->application_number ?? 'N/A';
                $context['application_stage'] = $lead->application?->application_stage ?? 'not_started';
                $context['fee_status'] = $lead->application?->fee_status ?? 'N/A';

                // Recent enquiry details
                $latestEnquiry = $lead->enquiries->last();
                if ($latestEnquiry) {
                    $context['latest_enquiry_question'] = $latestEnquiry->question;
                    $context['latest_enquiry_type'] = $latestEnquiry->inquiry_type;
                }

                // Recent follow-up details
                $latestFollowup = $lead->followUps->last();
                if ($latestFollowup) {
                    $context['latest_followup_subject'] = $latestFollowup->subject;
                    $context['latest_followup_notes'] = $latestFollowup->notes;
                    $context['latest_followup_type'] = $latestFollowup->type;
                }
            }
        }

        switch ($capability) {
            case 'student_summary_generator':
                if (! empty($entityIds['student_id']) && $user->hasPermissionTo('students.view')) {
                    $student = Student::withCount('notes')->where('school_id', $schoolId)->find($entityIds['student_id']);
                    if ($student) {
                        $context['student_id'] = $student->id;
                        $context['student_name'] = $student->full_name;
                        $context['status'] = $student->status;
                        $context['notes_count'] = $student->notes_count;
                    }
                }
                break;

            case 'followup_assistant':
                if ($user->hasPermissionTo('tasks.view')) {
                    $overdueCount = CrmTask::where('school_id', $schoolId)->where('status', 'overdue')->count();
                    $context['overdue_count'] = $overdueCount;
                }
                break;

            case 'parent_communication_assistant':
                if (! empty($entityIds['parent_id']) && $user->hasPermissionTo('parents.view')) {
                    $parent = ParentProfile::where('school_id', $schoolId)->find($entityIds['parent_id']);
                    if ($parent) {
                        $context['parent_id'] = $parent->id;
                        $context['parent_name'] = $parent->full_name;
                    }
                }
                break;

            case 'parent_communication_generator':
                $context['topic'] = $entityIds['topic'] ?? 'School Notice';
                $context['audience'] = $entityIds['audience'] ?? 'All Parents';
                $context['tone'] = $entityIds['tone'] ?? 'Formal';
                $context['language'] = $entityIds['language'] ?? 'en';
                $context['important_info'] = $entityIds['important_info'] ?? '';
                break;
        }

        return $context;
    }

    /**
     * Record usage audit log.
     */
    protected function logUsage(User $user, string $providerName, string $capability, AIResponse $response): AIUsageLog
    {
        return AIUsageLog::create([
            'school_id' => $user->school_id ?? 1,
            'user_id' => $user->id,
            'provider' => $providerName,
            'capability' => $capability,
            'prompt_tokens' => $response->promptTokens,
            'completion_tokens' => $response->completionTokens,
            'total_tokens' => $response->totalTokens,
            'cost_estimate' => $response->costEstimate,
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);
    }
}
