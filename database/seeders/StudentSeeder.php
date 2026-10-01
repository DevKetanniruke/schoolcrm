<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Division;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAddress;
use App\Models\StudentDocument;
use App\Models\StudentGuardian;
use App\Models\StudentNote;
use App\Models\StudentStatusHistory;
use App\Models\StudentTag;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('code', 'OIA-01')->first() ?? School::first();
        $ay = AcademicYear::where('school_id', $school->id)->first();
        $dept = Department::where('school_id', $school->id)->first();
        $class = SchoolClass::where('department_id', $dept->id)->first();
        $division = Division::where('class_id', $class->id)->first();
        $adminUser = User::where('email', 'admin@smartschool.edu')->first();

        // Sample Student 1: Alexander Vance
        $student1 = Student::create([
            'school_id' => $school->id,
            'student_id_number' => 'STU-2026-001',
            'admission_number' => 'ADM-9041',
            'first_name' => 'Alexander',
            'middle_name' => 'James',
            'last_name' => 'Vance',
            'date_of_birth' => '2010-05-14',
            'gender' => 'male',
            'email' => 'alex.vance@student.smartschool.edu',
            'phone' => '+1 (555) 019-8821',
            'academic_year_id' => $ay->id ?? null,
            'class_id' => $class->id ?? null,
            'division_id' => $division->id ?? null,
            'roll_number' => '101',
            'admission_date' => '2026-08-15',
            'status' => 'active',
        ]);

        StudentGuardian::create([
            'student_id' => $student1->id,
            'name' => 'Robert Vance',
            'relationship' => 'father',
            'phone' => '+1 (555) 999-4433',
            'email' => 'robert.vance@gmail.com',
            'occupation' => 'Senior Systems Engineer',
            'is_primary_contact' => true,
            'is_emergency_contact' => true,
        ]);

        StudentGuardian::create([
            'student_id' => $student1->id,
            'name' => 'Eleanor Vance',
            'relationship' => 'mother',
            'phone' => '+1 (555) 999-4434',
            'email' => 'eleanor.vance@gmail.com',
            'occupation' => 'Architect',
            'is_primary_contact' => false,
            'is_emergency_contact' => true,
        ]);

        StudentAddress::create([
            'student_id' => $student1->id,
            'type' => 'home',
            'address_line1' => '425 High Street',
            'city' => 'Springfield',
            'state' => 'IL',
            'postal_code' => '62701',
            'country' => 'USA',
            'is_primary' => true,
        ]);

        StudentTag::create(['student_id' => $student1->id, 'tag_name' => 'Honor Roll', 'color' => 'emerald']);
        StudentTag::create(['student_id' => $student1->id, 'tag_name' => 'Basketball Team', 'color' => 'indigo']);

        StudentNote::create([
            'student_id' => $student1->id,
            'author_id' => $adminUser->id ?? null,
            'note' => 'Alexander demonstrated exceptional mathematics performance during orientation evaluation.',
            'is_confidential' => false,
            'category' => 'academic',
        ]);

        StudentNote::create([
            'student_id' => $student1->id,
            'author_id' => $adminUser->id ?? null,
            'note' => 'Confidential guidance counselor review completed for scholarship eligibility.',
            'is_confidential' => true,
            'category' => 'general',
        ]);

        StudentDocument::create([
            'student_id' => $student1->id,
            'title' => 'Birth Certificate Verified Copy',
            'document_type' => 'ID Proof',
            'file_path' => 'documents/students/birth_cert_001.pdf',
            'file_size' => 1024500,
            'uploaded_by' => $adminUser->id ?? null,
        ]);

        StudentStatusHistory::create([
            'student_id' => $student1->id,
            'changed_by' => $adminUser->id ?? null,
            'from_status' => null,
            'to_status' => 'active',
            'reason' => 'Initial Admission Enrollment Completed',
        ]);

        // Sample Student 2: Sophia Martinez
        $student2 = Student::create([
            'school_id' => $school->id,
            'student_id_number' => 'STU-2026-002',
            'admission_number' => 'ADM-9042',
            'first_name' => 'Sophia',
            'middle_name' => 'Isabella',
            'last_name' => 'Martinez',
            'date_of_birth' => '2011-09-22',
            'gender' => 'female',
            'email' => 'sophia.m@student.smartschool.edu',
            'phone' => '+1 (555) 019-8822',
            'academic_year_id' => $ay->id ?? null,
            'class_id' => $class->id ?? null,
            'division_id' => $division->id ?? null,
            'roll_number' => '102',
            'admission_date' => '2026-08-16',
            'status' => 'active',
        ]);

        StudentGuardian::create([
            'student_id' => $student2->id,
            'name' => 'Carlos Martinez',
            'relationship' => 'father',
            'phone' => '+1 (555) 888-7711',
            'email' => 'carlos.martinez@corp.com',
            'occupation' => 'Physician',
            'is_primary_contact' => true,
            'is_emergency_contact' => true,
        ]);
    }
}
