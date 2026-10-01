<?php

namespace Tests\Feature;

use App\Core\AI\Services\AIService;
use App\Livewire\Admissions\Show360 as AdmissionShow360;
use App\Models\AdmissionLead;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AiAdmissionAssistantTest extends TestCase
{
    use RefreshDatabase;

    protected User $counselorUser;
    protected School $school;
    protected AdmissionLead $lead;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->school = School::create([
            'name' => 'AI Admission Test Academy',
            'code' => 'AATA-01',
            'status' => 'active',
        ]);

        $superAdminRole = Role::where('slug', 'super-admin')->first();

        $this->counselorUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Admission Counselor User',
            'email' => 'counselor@smartschool.test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $this->counselorUser->roles()->attach($superAdminRole->id);

        $this->lead = AdmissionLead::create([
            'school_id' => $this->school->id,
            'lead_code' => 'LEAD-AI-01',
            'parent_name' => 'Rajesh Sharma',
            'student_name' => 'Aarav Sharma',
            'parent_phone' => '+919876543210',
            'parent_email' => 'rajesh.sharma@example.com',
            'stage' => 'counselling',
            'status' => 'active',
            'score' => 85,
        ]);
    }

    public function test_all_eight_ai_admission_capabilities_execute_successfully()
    {
        /** @var AIService $aiService */
        $aiService = app(AIService::class);

        $capabilities = [
            'lead_history_summary' => 'Reason/Context',
            'lead_followup_suggestions' => 'Suggested Action',
            'draft_personalized_message' => 'Generated Content',
            'overdue_followups_identifier' => 'User Confirmation',
            'enquiry_conversation_summary' => 'Reason/Context',
            'counselor_call_notes' => 'Suggested Action',
            'visit_summaries' => 'Generated Content',
            'next_admin_actions' => 'User Confirmation',
        ];

        foreach ($capabilities as $capability => $expectedSection) {
            $response = $aiService->executeCapability(
                $capability,
                "Execute {$capability}",
                $this->counselorUser,
                ['lead_id' => $this->lead->id],
                'local_heuristic'
            );

            $this->assertTrue($response->success, "Capability {$capability} failed to execute.");
            $this->assertStringContainsString('Reason/Context', $response->content);
            $this->assertStringContainsString('Suggested Action', $response->content);
            $this->assertStringContainsString('Generated Content', $response->content);
            $this->assertStringContainsString('User Confirmation', $response->content);
        }
    }

    public function test_ai_admission_assistant_renders_in_lead_360_workspace()
    {
        Livewire::actingAs($this->counselorUser)
            ->test(AdmissionShow360::class, ['lead' => $this->lead])
            ->set('activeTab', 'ai_assistant')
            ->call('runAiAssistant', 'lead_history_summary')
            ->assertStatus(200)
            ->assertSee('AI Admission Assistant', false)
            ->assertSee('Reason/Context', false)
            ->assertSee('User Confirmation & Staff Approval', false);
    }

    public function test_ai_admission_assistant_supports_marathi_language()
    {
        app()->setLocale('mr');

        /** @var AIService $aiService */
        $aiService = app(AIService::class);

        $response = $aiService->executeCapability(
            'draft_personalized_message',
            'पालकांसाठी संदेश मसुदा तयार करा.',
            $this->counselorUser,
            ['lead_id' => $this->lead->id],
            'local_heuristic'
        );

        $this->assertTrue($response->success);
        $this->assertStringContainsString('पालकांसाठी वैयक्तिक संदेश मसुदा', $response->content);
        $this->assertStringContainsString('कारणे / संदर्भ (Reason/Context)', $response->content);
        $this->assertStringContainsString('वापरकर्ता संमती (User Confirmation)', $response->content);
    }
}
