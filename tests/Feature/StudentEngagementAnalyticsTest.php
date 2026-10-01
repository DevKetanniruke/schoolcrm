<?php

namespace Tests\Feature;

use App\Core\Services\StudentEngagementService;
use App\Livewire\Students\EngagementAnalytics;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentEngagementRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudentEngagementAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected User $authorizedUser;
    protected School $school;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->school = School::first();

        $this->authorizedUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Analytics Staff',
            'email' => 'analytics.staff@smartschool.edu',
            'password' => bcrypt('password123'),
        ]);

        $this->authorizedUser->assignRole('super-admin');

        $this->student = Student::where('school_id', $this->school->id)->first() ?? Student::factory()->create([
            'school_id' => $this->school->id,
            'first_name' => 'Aarav',
            'last_name' => 'Sharma',
            'admission_number' => 'ADM-ENG-001',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function authorized_user_can_access_student_engagement_analytics_dashboard()
    {
        $this->actingAs($this->authorizedUser);

        $response = $this->get(route('students.engagement-analytics'));

        $response->assertStatus(200);
        $response->assertSee('Student Engagement Analytics');
    }

    /** @test */
    public function engagement_service_tracks_all_six_indicators_and_uses_neutral_statuses()
    {
        $service = app(StudentEngagementService::class);

        $record = $service->evaluateStudent($this->student, [
            'attendance_days_present' => 90,
            'attendance_days_absent' => 2,
            'assignment_submissions_count' => 20,
            'assignment_pending_count' => 1,
            'communication_response_rate' => 95.0,
            'academic_gpa_score' => 92.0,
            'participation_score' => 88.0,
            'support_requests_count' => 0,
        ]);

        $this->assertNotNull($record);
        $this->assertEquals($this->student->id, $record->student_id);
        
        // Neutral Non-Stigmatizing Status check
        $this->assertContains($record->overall_status, ['normal', 'needs_attention', 'followup_recommended']);
        $this->assertNotEquals('at-risk', $record->overall_status);
        $this->assertNotEquals('failing', $record->overall_status);

        // All 6 indicators populated
        $this->assertGreaterThan(0, $record->attendance_percentage);
        $this->assertGreaterThan(0, $record->assignment_completion_rate);
        $this->assertGreaterThan(0, $record->communication_response_rate);
        $this->assertGreaterThan(0, $record->academic_gpa_score);
        $this->assertGreaterThan(0, $record->participation_score);
        $this->assertNotNull($record->underlying_data_json);
    }

    /** @test */
    public function underlying_evidence_is_stored_and_viewable_in_drawer()
    {
        $this->actingAs($this->authorizedUser);

        $service = app(StudentEngagementService::class);
        $record = $service->evaluateStudent($this->student);

        Livewire::test(EngagementAnalytics::class)
            ->call('viewUnderlyingEvidence', $this->student->id)
            ->assertSet('showUnderlyingDrawer', true)
            ->assertSee('Granular Underlying Evidence')
            ->assertSee($this->student->full_name);

        $this->assertArrayHasKey('attendance', $record->underlying_data_json);
        $this->assertArrayHasKey('assignments', $record->underlying_data_json);
        $this->assertArrayHasKey('communication', $record->underlying_data_json);
        $this->assertArrayHasKey('academics', $record->underlying_data_json);
        $this->assertArrayHasKey('participation', $record->underlying_data_json);
        $this->assertArrayHasKey('support_requests', $record->underlying_data_json);
    }

    /** @test */
    public function high_impact_decisions_are_never_executed_automatically()
    {
        $initialStatus = $this->student->status;

        $service = app(StudentEngagementService::class);

        // Evaluate student with low engagement scores
        $record = $service->evaluateStudent($this->student, [
            'attendance_days_present' => 40,
            'attendance_days_absent' => 30,
            'assignment_submissions_count' => 5,
            'assignment_pending_count' => 15,
            'academic_gpa_score' => 55.0,
        ]);

        $this->assertEquals('followup_recommended', $record->overall_status);

        // Verify Student status in core database was NOT changed automatically
        $this->student->refresh();
        $this->assertEquals($initialStatus, $this->student->status);
        $this->assertNotEquals('suspended', $this->student->status);
    }
}
