<?php

namespace App\Livewire\Admin;

use App\Models\CrmAutomationLog;
use App\Models\CrmAutomationRule;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AutomationRules extends Component
{
    use AuthorizesRequests, WithPagination;

    // Rule Builder Inputs
    public string $name = '';
    public string $trigger_event = 'lead.created'; // lead.created, followup.overdue, document.missing, enquiry.submitted
    public string $status = 'active';

    // Conditions Builder (Key-Value)
    public string $condition_field = '';
    public string $condition_operator = 'equals';
    public string $condition_value = '';
    public array $conditions = [];

    // Actions Builder
    public array $selected_actions = ['assign_counselor', 'create_followup', 'send_acknowledgement'];

    // UI Tab State
    public string $active_tab = 'rules'; // rules, audit_logs
    public ?int $editing_rule_id = null;

    public array $triggerOptions = [
        'lead.created' => 'WHEN: Admission lead created',
        'followup.overdue' => 'WHEN: Follow-up becomes overdue',
        'document.missing' => 'WHEN: Application document missing',
        'enquiry.submitted' => 'WHEN: Parent submits enquiry',
    ];

    public array $actionOptions = [
        'assign_counselor' => 'THEN: Assign counselor',
        'create_followup' => 'THEN: Create follow-up',
        'send_acknowledgement' => 'THEN: Send acknowledgement email',
        'create_reminder' => 'THEN: Create reminder alert',
        'notify_assigned_staff' => 'THEN: Notify assigned staff',
        'create_crm_activity' => 'THEN: Create CRM activity log',
    ];

    public function mount(): void
    {
        $this->ensureSampleRulesSeeded();
    }

    public function ensureSampleRulesSeeded(): void
    {
        $user = Auth::user();
        $schoolId = $user->school_id ?? School::first()->id ?? 1;

        if (CrmAutomationRule::where('school_id', $schoolId)->count() === 0) {
            // Seed 4 Core Automation Rules from User Prompt
            CrmAutomationRule::create([
                'school_id' => $schoolId,
                'name' => 'Lead Creation Workflow',
                'trigger_event' => 'lead.created',
                'conditions_json' => [
                    ['field' => 'stage', 'operator' => 'equals', 'value' => 'inquiry'],
                ],
                'actions_json' => [
                    ['action' => 'assign_counselor', 'params' => []],
                    ['action' => 'create_followup', 'params' => ['subject' => 'Initial Counseling Call', 'days' => 1]],
                    ['action' => 'send_acknowledgement', 'params' => ['subject' => 'Thank you for your interest in SmartSchool']],
                ],
                'status' => 'active',
                'created_by' => $user->id,
            ]);

            CrmAutomationRule::create([
                'school_id' => $schoolId,
                'name' => 'Overdue Follow-up Escalation',
                'trigger_event' => 'followup.overdue',
                'conditions_json' => [
                    ['field' => 'status', 'operator' => 'equals', 'value' => 'pending'],
                ],
                'actions_json' => [
                    ['action' => 'create_reminder', 'params' => []],
                    ['action' => 'notify_assigned_staff', 'params' => []],
                ],
                'status' => 'active',
                'created_by' => $user->id,
            ]);

            CrmAutomationRule::create([
                'school_id' => $schoolId,
                'name' => 'Missing Application Document Alert',
                'trigger_event' => 'document.missing',
                'conditions_json' => [],
                'actions_json' => [
                    ['action' => 'notify_assigned_staff', 'params' => []],
                ],
                'status' => 'active',
                'created_by' => $user->id,
            ]);

            CrmAutomationRule::create([
                'school_id' => $schoolId,
                'name' => 'Parent Enquiry Activity Logger',
                'trigger_event' => 'enquiry.submitted',
                'conditions_json' => [],
                'actions_json' => [
                    ['action' => 'create_crm_activity', 'params' => []],
                ],
                'status' => 'active',
                'created_by' => $user->id,
            ]);
        }
    }

    public function addCondition(): void
    {
        if ($this->condition_field && $this->condition_value) {
            $this->conditions[] = [
                'field' => $this->condition_field,
                'operator' => $this->condition_operator,
                'value' => $this->condition_value,
            ];

            $this->condition_field = '';
            $this->condition_value = '';
        }
    }

    public function removeCondition(int $index): void
    {
        unset($this->conditions[$index]);
        $this->conditions = array_values($this->conditions);
    }

    public function createRule(): void
    {
        $this->authorize('viewAny', User::class);

        $this->validate([
            'name' => 'required|string|min:3|max:150',
            'trigger_event' => 'required|string',
            'selected_actions' => 'required|array|min:1',
        ]);

        $user = Auth::user();
        $schoolId = $user->school_id ?? School::first()->id ?? 1;

        $actionsFormatted = array_map(function ($act) {
            return ['action' => $act, 'params' => []];
        }, $this->selected_actions);

        CrmAutomationRule::create([
            'school_id' => $schoolId,
            'name' => $this->name,
            'trigger_event' => $this->trigger_event,
            'conditions_json' => $this->conditions,
            'actions_json' => $actionsFormatted,
            'status' => $this->status,
            'created_by' => $user->id,
        ]);

        $this->reset(['name', 'conditions', 'selected_actions']);
        $this->selected_actions = ['assign_counselor'];

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Automation Rule Created',
            'message' => 'New CRM automation rule successfully saved and activated.',
        ]);
    }

    public function toggleRuleStatus(int $ruleId): void
    {
        $this->authorize('viewAny', User::class);

        $rule = CrmAutomationRule::find($ruleId);
        if ($rule) {
            $rule->update([
                'status' => $rule->status === 'active' ? 'inactive' : 'active',
            ]);

            $this->dispatch('notify', [
                'type' => 'info',
                'title' => 'Status Updated',
                'message' => "Rule status changed to '{$rule->status}'.",
            ]);
        }
    }

    public function deleteRule(int $ruleId): void
    {
        $this->authorize('viewAny', User::class);

        $rule = CrmAutomationRule::find($ruleId);
        if ($rule) {
            $rule->delete();

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Rule Deleted',
                'message' => 'CRM automation rule deleted.',
            ]);
        }
    }

    public function render()
    {
        $this->authorize('viewAny', User::class);

        $user = Auth::user();
        $schoolId = $user->school_id ?? 1;

        $rules = CrmAutomationRule::with('creator')
            ->where('school_id', $schoolId)
            ->latest()
            ->paginate(10);

        $auditLogs = CrmAutomationLog::with('rule')
            ->where('school_id', $schoolId)
            ->latest()
            ->paginate(15, ['*'], 'logsPage');

        return view('livewire.admin.automation-rules', [
            'rules' => $rules,
            'auditLogs' => $auditLogs,
        ])->layout('components.layouts.admin', ['title' => 'CRM Automation Engine']);
    }
}
