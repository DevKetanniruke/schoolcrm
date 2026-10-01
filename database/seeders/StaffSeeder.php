<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Division;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\StaffDocument;
use App\Models\StaffEvaluation;
use App\Models\StaffNote;
use App\Models\StaffProfile;
use App\Models\StaffSchedule;
use App\Models\StaffTask;
use App\Models\Subject;
use App\Models\TeacherClassAssignment;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('code', 'OIA-01')->first() ?? School::first();
        $academicYear = AcademicYear::where('school_id', $school->id)->first() ?? AcademicYear::first();
        $deptScience = Department::where('code', 'SCI')->first() ?? Department::first();
        $deptAdmin = Department::where('code', 'ADM')->first() ?? Department::create([
            'school_id' => $school->id,
            'name' => 'Administration & HR',
            'code' => 'ADM',
            'status' => 'active',
        ]);

        $grade5 = SchoolClass::where('code', 'GR5')->first() ?? SchoolClass::first();
        $grade3 = SchoolClass::where('code', 'GR3')->first() ?? SchoolClass::skip(1)->first() ?? $grade5;
        $divA = Division::where('class_id', $grade5->id)->first();

        // 1. Create Subjects (Physics & Chemistry)
        $physics = Subject::updateOrCreate(
            ['code' => 'PHY-101'],
            [
                'department_id' => $deptScience->id,
                'name' => 'Physics & Natural Sciences',
                'credits' => 4,
                'type' => 'core',
                'status' => 'active',
            ]
        );

        $chemistry = Subject::updateOrCreate(
            ['code' => 'CHM-101'],
            [
                'department_id' => $deptScience->id,
                'name' => 'Organic & Inorganic Chemistry',
                'credits' => 4,
                'type' => 'core',
                'status' => 'active',
            ]
        );

        // 2. Staff Member 1: Prof. Robert Vance (Senior Physics Teacher & Grade 5 Class Teacher)
        $robertUser = User::updateOrCreate(
            ['email' => 'robert.vance@smartschool.edu'],
            [
                'school_id' => $school->id,
                'name' => 'Prof. Robert Vance',
                'phone' => '+1 (555) 234-5678',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $teacherRole = Role::where('slug', 'teacher')->first();
        if ($teacherRole) {
            $robertUser->assignRole($teacherRole);
        }

        $robertStaff = StaffProfile::updateOrCreate(
            ['employee_code' => 'EMP-2024-001'],
            [
                'school_id' => $school->id,
                'user_id' => $robertUser->id,
                'department_id' => $deptScience->id,
                'first_name' => 'Robert',
                'middle_name' => 'Edward',
                'last_name' => 'Vance',
                'gender' => 'male',
                'date_of_birth' => '1982-04-12',
                'email' => 'robert.vance@smartschool.edu',
                'phone' => '+1 (555) 234-5678',
                'alternate_phone' => '+1 (555) 876-5432',
                'role_type' => 'teacher',
                'designation' => 'Senior Physics Teacher',
                'qualification' => 'M.Sc Physics, B.Ed',
                'experience_years' => 12,
                'joining_date' => '2018-08-15',
                'employment_type' => 'full_time',
                'address_line1' => '104 Academic Way',
                'city' => 'Springfield',
                'state' => 'IL',
                'postal_code' => '62704',
                'status' => 'active',
            ]
        );

        // Map Robert to Physics
        TeacherSubject::updateOrCreate(
            ['staff_id' => $robertStaff->id, 'subject_id' => $physics->id],
            [
                'academic_year_id' => $academicYear ? $academicYear->id : null,
                'is_primary' => true,
            ]
        );

        // Map Robert to Grade 5 (Class Teacher) and Grade 3 (Subject Teacher)
        TeacherClassAssignment::updateOrCreate(
            ['staff_id' => $robertStaff->id, 'class_id' => $grade5->id],
            [
                'division_id' => $divA ? $divA->id : null,
                'academic_year_id' => $academicYear ? $academicYear->id : null,
                'assignment_role' => 'class_teacher',
                'is_class_teacher' => true,
            ]
        );

        if ($grade3 && $grade3->id !== $grade5->id) {
            TeacherClassAssignment::updateOrCreate(
                ['staff_id' => $robertStaff->id, 'class_id' => $grade3->id],
                [
                    'academic_year_id' => $academicYear ? $academicYear->id : null,
                    'assignment_role' => 'subject_teacher',
                    'is_class_teacher' => false,
                ]
            );
        }

        // Staff Documents for Robert
        StaffDocument::updateOrCreate(
            ['staff_id' => $robertStaff->id, 'title' => 'M.Sc Degree Certificate'],
            [
                'document_type' => 'degree_certificate',
                'file_path' => 'staff_documents/robert_vance_msc_degree.pdf',
                'file_size' => 245000,
                'mime_type' => 'application/pdf',
                'verification_status' => 'verified',
                'uploaded_by' => $robertUser->id,
            ]
        );

        StaffDocument::updateOrCreate(
            ['staff_id' => $robertStaff->id, 'title' => 'State Teaching License'],
            [
                'document_type' => 'certification',
                'file_path' => 'staff_documents/robert_vance_license.pdf',
                'file_size' => 180000,
                'mime_type' => 'application/pdf',
                'verification_status' => 'verified',
                'expiry_date' => '2028-12-31',
                'uploaded_by' => $robertUser->id,
            ]
        );

        // Staff Tasks for Robert
        StaffTask::updateOrCreate(
            ['staff_id' => $robertStaff->id, 'title' => 'Prepare Grade 5 Mid-Term Physics Exam Papers'],
            [
                'assigned_by' => User::first()->id ?? null,
                'description' => 'Draft 50-mark question paper covering Newton laws & optics.',
                'due_date' => '2026-10-10',
                'priority' => 'high',
                'status' => 'pending',
            ]
        );

        StaffTask::updateOrCreate(
            ['staff_id' => $robertStaff->id, 'title' => 'Submit Q3 Syllabus Coverage Report'],
            [
                'assigned_by' => User::first()->id ?? null,
                'description' => 'Document completed chapters and practical lab experiments.',
                'due_date' => '2026-09-30',
                'priority' => 'medium',
                'status' => 'completed',
                'completed_at' => now()->subDays(2),
            ]
        );

        // Staff Notes for Robert
        StaffNote::updateOrCreate(
            ['staff_id' => $robertStaff->id, 'note' => 'Appointed Science Fair Committee Lead for 2026-2027.'],
            [
                'author_id' => User::first()->id ?? null,
                'category' => 'general',
                'is_confidential' => false,
            ]
        );

        StaffNote::updateOrCreate(
            ['staff_id' => $robertStaff->id, 'note' => 'Annual Performance Evaluation Rating: Exceeds Expectations (5/5).'],
            [
                'author_id' => User::first()->id ?? null,
                'category' => 'performance',
                'is_confidential' => true,
            ]
        );

        // Staff Evaluations for Robert
        StaffEvaluation::updateOrCreate(
            ['staff_id' => $robertStaff->id, 'review_period' => 'Annual 2025-2026'],
            [
                'evaluator_id' => User::first()->id ?? null,
                'rating' => 5,
                'feedback' => 'Exceptional classroom management, student engagement in physics lab projects, and active participation in parent-teacher meetings.',
                'goals' => 'Introduce interactive STEM robotics module next semester.',
                'evaluation_date' => '2026-06-15',
            ]
        );

        // Staff Timetable Schedules for Robert
        StaffSchedule::updateOrCreate(
            ['staff_id' => $robertStaff->id, 'day_of_week' => 'monday', 'start_time' => '09:00'],
            [
                'class_id' => $grade5->id,
                'subject_id' => $physics->id,
                'end_time' => '10:00',
                'room_number' => 'Lab 201',
            ]
        );

        StaffSchedule::updateOrCreate(
            ['staff_id' => $robertStaff->id, 'day_of_week' => 'wednesday', 'start_time' => '11:00'],
            [
                'class_id' => $grade5->id,
                'subject_id' => $physics->id,
                'end_time' => '12:00',
                'room_number' => 'Lab 201',
            ]
        );

        // 3. Staff Member 2: Dr. Eleanor Vance (Chemistry Lead)
        $eleanorUser = User::updateOrCreate(
            ['email' => 'eleanor.vance@smartschool.edu'],
            [
                'school_id' => $school->id,
                'name' => 'Dr. Eleanor Vance',
                'phone' => '+1 (555) 345-6789',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        if ($teacherRole) {
            $eleanorUser->assignRole($teacherRole);
        }

        $eleanorStaff = StaffProfile::updateOrCreate(
            ['employee_code' => 'EMP-2024-002'],
            [
                'school_id' => $school->id,
                'user_id' => $eleanorUser->id,
                'department_id' => $deptScience->id,
                'first_name' => 'Eleanor',
                'last_name' => 'Vance',
                'gender' => 'female',
                'email' => 'eleanor.vance@smartschool.edu',
                'phone' => '+1 (555) 345-6789',
                'role_type' => 'teacher',
                'designation' => 'Lead Chemistry Faculty',
                'qualification' => 'Ph.D Chemistry',
                'experience_years' => 15,
                'joining_date' => '2016-09-01',
                'employment_type' => 'full_time',
                'status' => 'active',
            ]
        );

        TeacherSubject::updateOrCreate(
            ['staff_id' => $eleanorStaff->id, 'subject_id' => $chemistry->id],
            [
                'academic_year_id' => $academicYear ? $academicYear->id : null,
                'is_primary' => true,
            ]
        );

        // 4. Staff Member 3: Sarah Jenkins (HR Officer)
        $hrUser = User::updateOrCreate(
            ['email' => 'sarah.jenkins@smartschool.edu'],
            [
                'school_id' => $school->id,
                'name' => 'Sarah Jenkins',
                'phone' => '+1 (555) 456-7890',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $hrRole = Role::where('slug', 'hr')->first() ?? Role::where('slug', 'school-admin')->first();
        if ($hrRole) {
            $hrUser->assignRole($hrRole);
        }

        StaffProfile::updateOrCreate(
            ['employee_code' => 'EMP-2024-003'],
            [
                'school_id' => $school->id,
                'user_id' => $hrUser->id,
                'department_id' => $deptAdmin->id,
                'first_name' => 'Sarah',
                'last_name' => 'Jenkins',
                'gender' => 'female',
                'email' => 'sarah.jenkins@smartschool.edu',
                'phone' => '+1 (555) 456-7890',
                'role_type' => 'hr',
                'designation' => 'Head of Human Resources',
                'qualification' => 'MBA Human Resource Management',
                'experience_years' => 10,
                'joining_date' => '2020-01-15',
                'employment_type' => 'full_time',
                'status' => 'active',
            ]
        );
    }
}
