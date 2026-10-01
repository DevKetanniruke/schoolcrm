<?php

namespace Tests\Feature;

use App\Core\AI\Providers\AnthropicProvider;
use App\Core\AI\Providers\LogLocalAiProvider;
use App\Core\AI\Providers\OpenAIProvider;
use App\Core\AI\Services\AIService;
use App\Livewire\Ai\AssistantWorkspace;
use App\Models\AdmissionLead;
use App\Models\AIUsageLog;
use App\Models\ParentProfile;
use App\Models\Role;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AiAssistantModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $unprivilegedUser;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->school = School::create([
            'name' => 'AI Test Academy',
            'code' => 'AITA-01',
            'status' => 'active',
        ]);

        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $studentRole = Role::where('slug', 'student')->first();

        $this->adminUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'AI Admin User',
            'email' => 'ai.admin@smartschool.test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->adminUser->assignRole($superAdminRole);

        $this->unprivilegedUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Unprivileged Student User',
            'email' => 'unprivileged@smartschool.test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->unprivilegedUser->assignRole($studentRole);
    }

    public function test_ai_provider_abstraction_layer_resolves_configured_drivers()
    {
        /** @var AIService $aiService */
        $aiService = app(AIService::class);

        $localProvider = $aiService->resolveProvider('local_heuristic');
        $this->assertInstanceOf(LogLocalAiProvider::class, $localProvider);
        $this->assertEquals('local_heuristic', $localProvider->getProviderName());

        $openAiProvider = $aiService->resolveProvider('openai');
        $this->assertInstanceOf(OpenAIProvider::class, $openAiProvider);

        $anthropicProvider = $aiService->resolveProvider('anthropic');
        $this->assertInstanceOf(AnthropicProvider::class, $anthropicProvider);
    }

    public function test_ai_service_executes_capabilities_and_logs_usage()
    {
        /** @var AIService $aiService */
        $aiService = app(AIService::class);

        $student = Student::create([
            'school_id' => $this->school->id,
            'student_id_number' => 'STU-AI-01',
            'admission_number' => 'ADM-AI-01',
            'first_name' => 'Grace',
            'last_name' => 'Hopper',
            'date_of_birth' => '2011-12-09',
            'gender' => 'female',
            'admission_date' => '2026-08-15',
            'status' => 'active',
        ]);

        $response = $aiService->executeCapability(
            'student_summary_generator',
            "Summarize this student's recent activity.",
            $this->adminUser,
            ['student_id' => $student->id]
        );

        $this->assertTrue($response->success);
        $this->assertStringContainsString('Grace Hopper', $response->content);
        $this->assertGreaterThan(0, $response->totalTokens);

        $this->assertDatabaseHas('ai_usage_logs', [
            'user_id' => $this->adminUser->id,
            'capability' => 'student_summary_generator',
            'provider' => 'local_heuristic',
        ]);
    }

    public function test_ai_service_enforces_rbac_permissions()
    {
        /** @var AIService $aiService */
        $aiService = app(AIService::class);

        // Unprivileged student user trying to query executive management metrics (requires users.view)
        $response = $aiService->executeCapability(
            'management_query_assistant',
            "Show executive school health metrics",
            $this->unprivilegedUser
        );

        $this->assertFalse($response->success);
        $this->assertStringContainsString('Access Denied', $response->content);
    }

    public function test_all_seven_ai_capabilities_execute_successfully()
    {
        /** @var AIService $aiService */
        $aiService = app(AIService::class);

        $capabilities = [
            'school_assistant' => 'General school question',
            'admission_lead_assistant' => 'Draft follow-up for lead',
            'communication_generator' => 'Create announcement',
            'followup_assistant' => 'Summarize overdue tasks',
            'student_summary_generator' => 'Summarize student',
            'parent_communication_assistant' => 'Draft parent response',
            'management_query_assistant' => 'Show school executive metrics',
        ];

        foreach ($capabilities as $capability => $prompt) {
            $response = $aiService->executeCapability(
                $capability,
                $prompt,
                $this->adminUser
            );

            $this->assertTrue($response->success, "Capability {$capability} failed.");
            $this->assertNotEmpty($response->content);
        }
    }

    public function test_daily_rate_limiting_blocks_excessive_requests()
    {
        config(['ai.limits.daily_requests_per_user' => 2]);

        /** @var AIService $aiService */
        $aiService = app(AIService::class);

        // First 2 requests succeed
        $r1 = $aiService->executeCapability('school_assistant', 'Query 1', $this->adminUser);
        $r2 = $aiService->executeCapability('school_assistant', 'Query 2', $this->adminUser);
        $this->assertTrue($r1->success);
        $this->assertTrue($r2->success);

        // 3rd request fails due to rate limit
        $r3 = $aiService->executeCapability('school_assistant', 'Query 3', $this->adminUser);
        $this->assertFalse($r3->success);
        $this->assertStringContainsString('Usage Limit Exceeded', $r3->content);
    }

    public function test_livewire_ai_assistant_workspace_renders_and_processes_prompts()
    {
        Livewire::actingAs($this->adminUser)
            ->test(AssistantWorkspace::class)
            ->assertStatus(200)
            ->assertSee('SmartSchool AI Copilot Layer', false)
            ->set('prompt', 'Draft a polite follow-up message for this admission lead.')
            ->set('selectedCapability', 'admission_lead_assistant')
            ->call('submitPrompt')
            ->assertStatus(200)
            ->assertSee('Reason/Context', false);
    }
}
