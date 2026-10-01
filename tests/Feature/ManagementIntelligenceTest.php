<?php

namespace Tests\Feature;

use App\Core\AI\Services\ManagementQueryEngine;
use App\Livewire\Admin\ManagementIntelligence;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManagementIntelligenceTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->school = School::first();

        $this->adminUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Management Administrator',
            'email' => 'admin.intelligence@smartschool.edu',
            'password' => bcrypt('password123'),
        ]);

        $this->adminUser->assignRole('super-admin');
    }

    /** @test */
    public function authorized_admin_can_access_management_intelligence_dashboard()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.management-intelligence'));

        $response->assertStatus(200);
        $response->assertSee('Management Intelligence Dashboard');
        $response->assertSee('Controlled Query Layer');
    }

    /** @test */
    public function management_query_engine_executes_safe_controlled_queries_for_example_questions()
    {
        $engine = app(ManagementQueryEngine::class);

        $questions = [
            "How many admission leads are pending follow-up?",
            "Which classes have attendance below the configured threshold?",
            "Show today's unresolved complaints.",
            "Summarize admission performance this month.",
            "Which follow-ups are overdue?",
        ];

        foreach ($questions as $q) {
            $result = $engine->executeQuery($q, $this->adminUser);

            // Output format structure assertion
            $this->assertArrayHasKey('answer', $result);
            $this->assertArrayHasKey('supporting_metrics', $result);
            $this->assertArrayHasKey('relevant_records', $result);
            $this->assertArrayHasKey('date_range', $result);
            $this->assertArrayHasKey('filters_used', $result);

            // Verify security constraint: query_type is controlled_eloquent_query
            $this->assertEquals('controlled_eloquent_query', $result['filters_used']['query_type']);
            $this->assertNotEmpty($result['answer']);
            $this->assertIsArray($result['supporting_metrics']);
            $this->assertIsArray($result['relevant_records']);
        }
    }

    /** @test */
    public function livewire_component_processes_queries_and_renders_structured_sections()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(ManagementIntelligence::class)
            ->call('runQuery', 'How many admission leads are pending follow-up?')
            ->assertSee('Executive Answer')
            ->assertSee('Supporting Metrics')
            ->assertSee('Relevant Records')
            ->assertSee('Drill Down');
    }

    /** @test */
    public function drill_down_links_are_generated_for_relevant_records()
    {
        $engine = app(ManagementQueryEngine::class);

        $result = $engine->executeQuery("Summarize admission performance this month.", $this->adminUser);

        $records = $result['relevant_records'];
        $this->assertIsArray($records);

        if (count($records) > 0) {
            $this->assertArrayHasKey('drill_down_url', $records[0]);
            $this->assertNotEmpty($records[0]['drill_down_url']);
        }
    }
}
