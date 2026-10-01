<?php

namespace Tests\Feature;

use App\Livewire\Communication\AiParentCommunication;
use App\Models\AiCommunicationDraft;
use App\Models\Communication;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AiParentCommunicationTest extends TestCase
{
    use RefreshDatabase;

    protected User $authorizedUser;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->school = School::first();

        $this->authorizedUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Admissions & Communication Staff',
            'email' => 'comm.staff.test@smartschool.edu',
            'password' => bcrypt('password123'),
        ]);

        $this->authorizedUser->assignRole('super-admin');
    }

    /** @test */
    public function authorized_staff_can_access_ai_parent_communication_studio()
    {
        $this->actingAs($this->authorizedUser);

        $response = $this->get(route('communication.ai-assistant'));

        $response->assertStatus(200);
        $response->assertSee('AI-Assisted Parent Communication Studio');
    }

    /** @test */
    public function ai_generates_all_five_multichannel_formats_in_english()
    {
        $this->actingAs($this->authorizedUser);

        Livewire::test(AiParentCommunication::class)
            ->set('topic', 'Annual Sports Day 2026')
            ->set('audience', 'all_parents')
            ->set('tone', 'formal')
            ->set('language', 'en')
            ->set('important_info', 'Event begins at 8:30 AM in the main stadium. Full sports uniform required.')
            ->call('generate')
            ->assertSet('approval_status', 'pending_approval')
            ->assertSee('[Official Circular]')
            ->assertSet('email_subject', '[Official Circular] Annual Sports Day 2026 - SmartSchool Academy');

        $this->assertDatabaseHas('ai_communication_drafts', [
            'topic' => 'Annual Sports Day 2026',
            'language' => 'en',
            'approval_status' => 'pending_approval',
        ]);
    }

    /** @test */
    public function ai_generates_multichannel_formats_in_marathi()
    {
        $this->actingAs($this->authorizedUser);

        Livewire::test(AiParentCommunication::class)
            ->set('topic', 'शाळा वार्षिक क्रीडा महोत्सव २०२६')
            ->set('audience', 'all_parents')
            ->set('tone', 'polite_friendly')
            ->set('language', 'mr')
            ->set('important_info', 'सकाळी ८:३० वाजता क्रीडा संकुलात उपस्थित राहावे.')
            ->call('generate')
            ->assertSet('language', 'mr')
            ->assertSee('स्मार्टस्कूल');

        $this->assertDatabaseHas('ai_communication_drafts', [
            'language' => 'mr',
        ]);
    }

    /** @test */
    public function ai_generates_multichannel_formats_in_hindi()
    {
        $this->actingAs($this->authorizedUser);

        Livewire::test(AiParentCommunication::class)
            ->set('topic', 'वार्षिक खेल दिवस समारोह 2026')
            ->set('audience', 'all_parents')
            ->set('tone', 'formal')
            ->set('language', 'hi')
            ->set('important_info', 'सभी विद्यार्थी सुबह 8:30 बजे मुख्य मैदान में उपस्थित हों।')
            ->call('generate')
            ->assertSet('language', 'hi')
            ->assertSee('स्मार्टस्कूल');

        $this->assertDatabaseHas('ai_communication_drafts', [
            'language' => 'hi',
        ]);
    }

    /** @test */
    public function staff_can_edit_preview_and_approve_draft()
    {
        $this->actingAs($this->authorizedUser);

        $component = Livewire::test(AiParentCommunication::class)
            ->set('topic', 'Mid-Term Exam Schedule')
            ->set('audience', 'grade_parents')
            ->set('tone', 'informative')
            ->set('language', 'en')
            ->call('generate')
            ->set('email_subject', 'Customized Subject: Mid-Term Exam Schedule 2026')
            ->call('saveEdit')
            ->call('approve')
            ->assertSet('approval_status', 'approved');

        $draftId = $component->get('draft_id');

        $this->assertDatabaseHas('ai_communication_drafts', [
            'id' => $draftId,
            'email_subject' => 'Customized Subject: Mid-Term Exam Schedule 2026',
            'approval_status' => 'approved',
        ]);
    }

    /** @test */
    public function safety_rule_prevents_sending_unapproved_ai_communication()
    {
        $this->actingAs($this->authorizedUser);

        $initialCount = Communication::count();

        // Generate a draft (approval_status will be 'pending_approval')
        $component = Livewire::test(AiParentCommunication::class)
            ->set('topic', 'Urgent Fee Reminder')
            ->set('audience', 'all_parents')
            ->set('tone', 'urgent_important')
            ->set('language', 'en')
            ->set('require_approval', true)
            ->call('generate');

        // Attempting to send while pending approval should trigger safety block
        $component->call('send');
        $component->assertSet('approval_status', 'pending_approval');

        // Ensure no communication record was dispatched
        $this->assertEquals($initialCount, Communication::count());

        // Now approve the draft and send
        $component->call('approve');
        $component->call('send');

        $component->assertSet('approval_status', 'sent');
        $this->assertEquals($initialCount + 1, Communication::count());
    }
}
