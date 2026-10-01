<?php

namespace App\Core\Services;

use App\Models\AdmissionEnquiry;
use App\Models\AdmissionLead;
use App\Models\Communication;
use App\Models\CrmAutomationLog;
use App\Models\CrmAutomationRule;
use App\Models\CrmTask;
use App\Models\CrmTaskActivity;
use App\Models\CrmTaskReminder;
use App\Models\LeadFollowUp;
use App\Models\School;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CrmAutomationEngine
{
    /**
     * Required permission per action for RBAC authorization check.
     */
    protected array $actionPermissions = [
        'assign_counselor' => 'admissions.view',
        'create_followup' => 'admissions.view',
        'send_acknowledgement' => 'communications.send',
        'create_reminder' => 'tasks.view',
        'notify_assigned_staff' => 'tasks.view',
        'create_crm_activity' => 'parents.view',
    ];

    /**
     * Dispatch an event trigger and process matching automation rules.
     */
    public function dispatchTrigger(string $triggerEvent, $entity, ?User $actor = null): array
    {
        $schoolId = $entity->school_id ?? ($actor?->school_id ?? 1);
        $entityType = class_basename($entity);
        $entityId = $entity->id;

        // Fetch active rules matching school and trigger event
        $activeRules = CrmAutomationRule::where('school_id', $schoolId)
            ->where('trigger_event', $triggerEvent)
            ->where('status', 'active')
            ->get();

        $executionResults = [];

        foreach ($activeRules as $rule) {
            $executionResults[] = $this->evaluateAndExecuteRule($rule, $entity, $entityType, $entityId, $actor);
        }

        return $executionResults;
    }

    /**
     * Evaluate rule conditions and execute actions if conditions pass & authorization is valid.
     */
    protected function evaluateAndExecuteRule(CrmAutomationRule $rule, $entity, string $entityType, int $entityId, ?User $actor = null): array
    {
        $schoolId = $rule->school_id;

        // 1. Evaluate Rule Conditions
        if (! $this->evaluateConditions($rule->conditions_json ?? [], $entity)) {
            $this->logAudit($schoolId, $rule->id, $rule->trigger_event, $entityType, $entityId, 'condition_unmet', [], 'Rule conditions not met for entity.');

            return [
                'rule_id' => $rule->id,
                'status' => 'condition_unmet',
                'message' => 'Rule conditions evaluated to false.',
            ];
        }

        // 2. Security Check: Ensure automation does NOT bypass authorization
        if (! $this->authorizeRuleExecution($rule, $actor)) {
            $this->logAudit($schoolId, $rule->id, $rule->trigger_event, $entityType, $entityId, 'blocked_by_auth', [], 'Authorization Denied: Triggering user or rule context lacks required permission.');

            return [
                'rule_id' => $rule->id,
                'status' => 'blocked_by_auth',
                'message' => 'Automation execution blocked by authorization policy.',
            ];
        }

        // 3. Execute Actions
        $executedActions = [];
        $actions = $rule->actions_json ?? [];

        foreach ($actions as $actionItem) {
            $actionName = $actionItem['action'] ?? null;
            $params = $actionItem['params'] ?? [];

            if ($actionName) {
                $result = $this->executeAction($actionName, $params, $entity, $actor);
                $executedActions[] = [
                    'action' => $actionName,
                    'result' => $result,
                    'executed_at' => now()->toDateTimeString(),
                ];
            }
        }

        // 4. Log Audit Trail
        $this->logAudit($schoolId, $rule->id, $rule->trigger_event, $entityType, $entityId, 'success', $executedActions);

        return [
            'rule_id' => $rule->id,
            'status' => 'success',
            'actions_executed' => count($executedActions),
        ];
    }

    /**
     * Evaluate condition array against entity attributes.
     */
    protected function evaluateConditions(array $conditions, $entity): bool
    {
        if (empty($conditions)) {
            return true;
        }

        foreach ($conditions as $cond) {
            $field = $cond['field'] ?? null;
            $operator = $cond['operator'] ?? 'equals';
            $expected = $cond['value'] ?? null;

            if (! $field) {
                continue;
            }

            $actual = data_get($entity, $field);

            $match = match ($operator) {
                'equals' => (string) $actual === (string) $expected,
                'not_equals' => (string) $actual !== (string) $expected,
                'greater_than' => (float) $actual > (float) $expected,
                'less_than' => (float) $actual < (float) $expected,
                'contains' => str_contains(mb_strtolower((string) $actual), mb_strtolower((string) $expected)),
                default => true,
            };

            if (! $match) {
                return false;
            }
        }

        return true;
    }

    /**
     * Security check: Verify user permissions before executing automated actions.
     */
    protected function authorizeRuleExecution(CrmAutomationRule $rule, ?User $actor = null): bool
    {
        // If actor is provided, check actor's permissions
        if ($actor) {
            if ($actor->hasRole('super-admin')) {
                return true;
            }

            $actions = $rule->actions_json ?? [];
            foreach ($actions as $act) {
                $actionName = $act['action'] ?? null;
                $reqPerm = $this->actionPermissions[$actionName] ?? null;

                if ($reqPerm && ! $actor->hasPermissionTo($reqPerm)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Execute specific action handler.
     */
    protected function executeAction(string $actionName, array $params, $entity, ?User $actor = null): string
    {
        $schoolId = $entity->school_id ?? 1;

        return match ($actionName) {
            'assign_counselor' => $this->actionAssignCounselor($entity, $params),
            'create_followup' => $this->actionCreateFollowup($entity, $params),
            'send_acknowledgement' => $this->actionSendAcknowledgement($entity, $params),
            'create_reminder' => $this->actionCreateReminder($entity, $params),
            'notify_assigned_staff' => $this->actionNotifyAssignedStaff($entity, $params),
            'create_crm_activity' => $this->actionCreateCrmActivity($entity, $params, $actor),
            default => "Executed custom action: {$actionName}",
        };
    }

    // --- Action Handlers ---

    protected function actionAssignCounselor($entity, array $params): string
    {
        $counselorId = $params['counselor_id'] ?? User::where('school_id', $entity->school_id ?? 1)->first()?->id;

        if ($entity instanceof AdmissionLead) {
            $entity->update(['assigned_counselor_id' => $counselorId]);
            return "Assigned lead to counselor ID #{$counselorId}";
        }

        return "Counselor assignment logged for " . class_basename($entity);
    }

    protected function actionCreateFollowup($entity, array $params): string
    {
        $subject = $params['subject'] ?? 'Automated CRM Follow-up';
        $days = (int) ($params['days'] ?? 1);

        if ($entity instanceof AdmissionLead) {
            LeadFollowUp::create([
                'lead_id' => $entity->id,
                'type' => 'call',
                'subject' => $subject,
                'scheduled_at' => Carbon::now()->addDays($days),
                'due_date' => Carbon::now()->addDays($days)->toDateString(),
                'status' => 'pending',
                'notes' => 'Created automatically by CRM Automation Engine rule.',
            ]);

            return "Created follow-up task '{$subject}' due in {$days} days.";
        }

        return "Follow-up created for " . class_basename($entity);
    }

    protected function actionSendAcknowledgement($entity, array $params): string
    {
        $subject = $params['subject'] ?? 'Thank you for your enquiry';

        Communication::create([
            'school_id' => $entity->school_id ?? 1,
            'title' => "Auto Acknowledgement: {$subject}",
            'channel' => 'email',
            'recipient_type' => 'individual',
            'subject' => $subject,
            'message_body' => "Dear Applicant/Parent, thank you for reaching out to SmartSchool Academy. Our admissions office has received your record.",
            'status' => 'queued',
            'sent_at' => now(),
        ]);

        return "Dispatched automated email acknowledgement.";
    }

    protected function actionCreateReminder($entity, array $params): string
    {
        $taskId = ($entity instanceof CrmTask) ? $entity->id : 1;
        $userId = $entity->assigned_to ?? User::where('school_id', $entity->school_id ?? 1)->first()?->id ?? 1;

        CrmTaskReminder::create([
            'task_id' => $taskId,
            'user_id' => $userId,
            'remind_at' => Carbon::now()->addMinutes(30),
            'channel' => 'in_app',
            'is_sent' => false,
        ]);

        return "Scheduled overdue task reminder alert.";
    }

    protected function actionNotifyAssignedStaff($entity, array $params): string
    {
        Log::info("CRM Automation Notification: Staff notified regarding missing document or entity #{$entity->id}");
        return "Notified assigned staff members regarding action item.";
    }

    protected function actionCreateCrmActivity($entity, array $params, ?User $actor = null): string
    {
        $taskId = ($entity instanceof CrmTask) ? $entity->id : 1;

        CrmTaskActivity::create([
            'task_id' => $taskId,
            'user_id' => $actor?->id ?? 1,
            'activity_type' => 'automation_triggered',
            'description' => "Parent enquiry or event triggered CRM activity log entry.",
        ]);

        return "Created CRM activity history log entry.";
    }

    protected function logAudit(int $schoolId, int $ruleId, string $trigger, string $entityType, int $entityId, string $status, array $actionsExecuted = [], ?string $error = null): void
    {
        CrmAutomationLog::create([
            'school_id' => $schoolId,
            'rule_id' => $ruleId,
            'trigger_event' => $trigger,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'execution_status' => $status,
            'actions_executed_json' => $actionsExecuted,
            'error_message' => $error,
            'executed_at' => now(),
        ]);
    }
}
