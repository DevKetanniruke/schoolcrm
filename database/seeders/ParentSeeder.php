<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\ParentAppointment;
use App\Models\ParentCommunication;
use App\Models\ParentComplaint;
use App\Models\ParentDocument;
use App\Models\ParentNote;
use App\Models\ParentProfile;
use App\Models\ParentTask;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('code', 'OIA-01')->first() ?? School::first();
        $academicYear = AcademicYear::where('school_id', $school->id)->first() ?? AcademicYear::first();
        $dept = Department::where('school_id', $school->id)->first() ?? Department::first();

        // 1. Get or Create Classes (Grade 5 and Grade 3)
        $grade5 = SchoolClass::updateOrCreate(
            ['code' => 'GR5'],
            [
                'department_id' => $dept->id,
                'name' => 'Grade 5',
                'year_level' => 5,
                'status' => 'active',
            ]
        );

        $grade3 = SchoolClass::updateOrCreate(
            ['code' => 'GR3'],
            [
                'department_id' => $dept->id,
                'name' => 'Grade 3',
                'year_level' => 3,
                'status' => 'active',
            ]
        );

        // 2. Create Portal User for Jane Doe
        $janeUser = User::updateOrCreate(
            ['email' => 'jane@example.com'],
            [
                'school_id' => $school->id,
                'name' => 'Jane Doe',
                'phone' => '123-456-7890',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        $parentRole = Role::where('slug', 'parent')->first();
        if ($parentRole) {
            $janeUser->assignRole($parentRole);
        }

        // 3. Create Parent Profile for Jane Doe (Required sample data)
        $janeParent = ParentProfile::updateOrCreate(
            ['email' => 'jane@example.com'],
            [
                'school_id' => $school->id,
                'user_id' => $janeUser->id,
                'parent_code' => 'PAR-2024-001',
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'phone' => '123-456-7890',
                'occupation' => 'Architect',
                'address_line1' => '742 Evergreen Terrace',
                'city' => 'Springfield',
                'state' => 'IL',
                'postal_code' => '62701',
                'status' => 'active',
            ]
        );

        // 4. Create Children: John Doe (Grade 5) and Emma Doe (Grade 3)
        $john = Student::updateOrCreate(
            ['admission_number' => 'ADM-2024-001'],
            [
                'school_id' => $school->id,
                'student_id_number' => 'STU-2024-001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'date_of_birth' => '2014-05-12',
                'gender' => 'male',
                'academic_year_id' => $academicYear ? $academicYear->id : null,
                'class_id' => $grade5->id,
                'admission_date' => '2024-01-10',
                'status' => 'active',
            ]
        );

        $emma = Student::updateOrCreate(
            ['admission_number' => 'ADM-2024-002'],
            [
                'school_id' => $school->id,
                'student_id_number' => 'STU-2024-002',
                'first_name' => 'Emma',
                'last_name' => 'Doe',
                'date_of_birth' => '2016-08-20',
                'gender' => 'female',
                'academic_year_id' => $academicYear ? $academicYear->id : null,
                'class_id' => $grade3->id,
                'admission_date' => '2024-01-10',
                'status' => 'active',
            ]
        );

        // 5. Link Parent to Children in Pivot
        $janeParent->students()->syncWithoutDetaching([
            $john->id => [
                'relationship_type' => 'mother',
                'is_primary_contact' => true,
                'is_emergency_contact' => true,
                'has_custody' => true,
                'financial_responsibility' => true,
                'portal_access_enabled' => true,
            ],
            $emma->id => [
                'relationship_type' => 'mother',
                'is_primary_contact' => true,
                'is_emergency_contact' => true,
                'has_custody' => true,
                'financial_responsibility' => true,
                'portal_access_enabled' => true,
            ],
        ]);

        // 6. Communication History (Exact required sample entries)
        ParentCommunication::updateOrCreate(
            ['parent_id' => $janeParent->id, 'interaction_date' => '2024-02-15 10:00:00'],
            [
                'school_id' => $school->id,
                'student_id' => $john->id,
                'type' => 'phone_call',
                'direction' => 'outbound',
                'subject' => 'Parent-Teacher Meeting',
                'summary' => '2024-02-15: Discussed upcoming parent-teacher meeting',
                'status' => 'completed',
            ]
        );

        ParentCommunication::updateOrCreate(
            ['parent_id' => $janeParent->id, 'interaction_date' => '2024-03-01 14:30:00'],
            [
                'school_id' => $school->id,
                'student_id' => $emma->id,
                'type' => 'email',
                'direction' => 'outbound',
                'subject' => 'Enrollment Form Submission',
                'summary' => '2024-03-01: Sent enrollment form',
                'status' => 'completed',
            ]
        );

        // 7. Documents (Exact required sample entries)
        ParentDocument::updateOrCreate(
            ['parent_id' => $janeParent->id, 'title' => 'ID proof'],
            [
                'student_id' => $john->id,
                'document_type' => 'identity_proof',
                'file_path' => 'parent_documents/jane_doe_id_proof.pdf',
                'file_size' => 102400,
                'mime_type' => 'application/pdf',
                'uploaded_by' => $janeUser->id,
            ]
        );

        ParentDocument::updateOrCreate(
            ['parent_id' => $janeParent->id, 'title' => 'Consent form'],
            [
                'student_id' => $emma->id,
                'document_type' => 'consent_form',
                'file_path' => 'parent_documents/jane_doe_consent_form.pdf',
                'file_size' => 204800,
                'mime_type' => 'application/pdf',
                'uploaded_by' => $janeUser->id,
            ]
        );

        // 8. Sample Appointments, Complaints, Tasks, & Notes for complete 360 view
        ParentAppointment::updateOrCreate(
            ['parent_id' => $janeParent->id, 'title' => 'Mid-Term Parent Teacher Meeting'],
            [
                'school_id' => $school->id,
                'student_id' => $john->id,
                'type' => 'parent_teacher_meeting',
                'scheduled_at' => '2026-10-05 10:00:00',
                'duration_minutes' => 30,
                'location_or_link' => 'Conference Room A / Zoom Link',
                'status' => 'scheduled',
                'meeting_notes' => 'Review math performance & science project progress.',
            ]
        );

        ParentComplaint::updateOrCreate(
            ['parent_id' => $janeParent->id, 'ticket_number' => 'CMP-2024-001'],
            [
                'school_id' => $school->id,
                'student_id' => $john->id,
                'category' => 'transport',
                'priority' => 'medium',
                'status' => 'resolved',
                'subject' => 'School Bus Route Timing Adjustment',
                'description' => 'Requested bus stop arrival time update by 10 minutes.',
                'resolution_notes' => 'Bus driver notified, route updated on Feb 20.',
                'resolved_at' => '2024-02-20 16:00:00',
            ]
        );

        ParentTask::updateOrCreate(
            ['parent_id' => $janeParent->id, 'title' => 'Submit Medical Immunization Record'],
            [
                'school_id' => $school->id,
                'student_id' => $emma->id,
                'due_date' => '2026-10-15',
                'priority' => 'high',
                'status' => 'pending',
                'description' => 'Upload updated booster vaccine certificate.',
            ]
        );

        ParentNote::updateOrCreate(
            ['parent_id' => $janeParent->id, 'note' => 'Active PTA committee volunteer member.'],
            [
                'author_id' => User::first()->id ?? null,
                'category' => 'general',
                'is_confidential' => false,
            ]
        );
    }
}
