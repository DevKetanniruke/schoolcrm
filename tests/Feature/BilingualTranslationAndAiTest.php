<?php

namespace Tests\Feature;

use App\Core\AI\Services\AIService;
use App\Livewire\Ai\AssistantWorkspace;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BilingualTranslationAndAiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->school = School::create([
            'name' => 'Translation Test Academy',
            'code' => 'TTA-01',
            'status' => 'active',
        ]);

        $superAdminRole = Role::where('slug', 'super-admin')->first();

        $this->user = User::create([
            'school_id' => $this->school->id,
            'name' => 'Translation Admin User',
            'email' => 'trans.admin@smartschool.test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $this->user->roles()->attach($superAdminRole->id);
    }

    public function test_language_switcher_route_sets_session_and_locale()
    {
        $response = $this->get('/lang/mr');
        $response->assertSessionHas('locale', 'mr');
        $response->assertRedirect();

        $responseEn = $this->get('/lang/en');
        $responseEn->assertSessionHas('locale', 'en');
        $responseEn->assertRedirect();
    }

    public function test_ai_service_generates_marathi_completions_when_locale_is_mr()
    {
        app()->setLocale('mr');
        /** @var AIService $aiService */
        $aiService = app(AIService::class);

        $response = $aiService->executeCapability(
            'communication_generator',
            'उद्याच्या शालेय कार्यक्रमाबद्दल पालकांसाठी सूचना तयार करा.',
            $this->user,
            ['driver' => 'local_heuristic']
        );

        $this->assertTrue($response->success);
        $this->assertStringContainsString('एआय निर्मित पालक आणि विद्यार्थी सूचना पत्रक', $response->content);
        $this->assertStringContainsString('शाळा प्रशासन', $response->content);
    }

    public function test_ai_service_detects_devanagari_marathi_script_in_prompt()
    {
        app()->setLocale('en');
        /** @var AIService $aiService */
        $aiService = app(AIService::class);

        // Prompt written in Marathi Devanagari script even when app locale is 'en'
        $response = $aiService->executeCapability(
            'student_summary_generator',
            'या विद्यार्थ्याच्या अलीकडील क्रियाकलापांचा सारांश द्या.',
            $this->user,
            ['driver' => 'local_heuristic']
        );

        $this->assertTrue($response->success);
        $this->assertStringContainsString('विद्यार्थी 360° माहिती सारांश', $response->content);
    }

    public function test_livewire_assistant_workspace_renders_marathi_interface_and_presets()
    {
        session(['locale' => 'mr']);
        app()->setLocale('mr');

        Livewire::actingAs($this->user)
            ->test(AssistantWorkspace::class)
            ->assertStatus(200)
            ->assertSee('SmartSchool AI Copilot Layer (मराठी)', false)
            ->assertSee('शाळा एआय सहाय्यक', false)
            ->set('selectedCapability', 'communication_generator')
            ->set('prompt', 'उद्याच्या शालेय कार्यक्रमाबद्दल पालकांसाठी सूचना तयार करा.')
            ->call('submitPrompt')
            ->assertStatus(200)
            ->assertSee('एआय निर्मित पालक आणि विद्यार्थी सूचना पत्रक', false);
    }

    public function test_sidebar_and_student_directory_render_in_marathi()
    {
        session(['locale' => 'mr']);
        app()->setLocale('mr');

        $response = $this->actingAs($this->user)->get('/students');
        $response->assertStatus(200);
        $response->assertSee('विद्यार्थी डिरेक्टरी', false);
        $response->assertSee('मुख्य नियंत्रण कक्ष', false);
        $response->assertSee('विद्यार्थी व्यवस्थापन', false);
        $response->assertSee('नवीन विद्यार्थी नोंदवा', false);
    }
}
