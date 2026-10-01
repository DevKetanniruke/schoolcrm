<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Division;
use App\Models\Organization;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class HierarchySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Organization
        $org = Organization::firstOrCreate(
            ['code' => 'OEF-GLOBAL'],
            [
                'name' => 'Oakridge Educational Foundation',
                'contact_email' => 'admin@oakridgefoundation.edu',
                'phone' => '+1 (555) 000-1122',
                'address' => '100 Global Way, Corporate Tower, NY',
                'status' => 'active',
            ]
        );

        // 2. School 1
        $school1 = School::firstOrCreate(
            ['code' => 'OIA-01'],
            [
                'organization_id' => $org->id,
                'name' => 'Oakridge International Academy',
                'domain' => 'oakridge.smartschool.edu',
                'email' => 'contact@oakridge.edu',
                'phone' => '+1 (555) 019-2834',
                'address' => '742 Evergreen Terrace, Springfield',
                'status' => 'active',
            ]
        );

        // 3. Campuses for School 1
        $campus1 = Campus::firstOrCreate(
            ['code' => 'MAIN-01', 'school_id' => $school1->id],
            [
                'name' => 'Main Academic Campus',
                'location' => 'Springfield West',
                'phone' => '+1 (555) 019-2835',
                'status' => 'active',
            ]
        );

        $campus2 = Campus::firstOrCreate(
            ['code' => 'NORTH-02', 'school_id' => $school1->id],
            [
                'name' => 'North Athletic Campus',
                'location' => 'Springfield North',
                'phone' => '+1 (555) 019-2836',
                'status' => 'active',
            ]
        );

        // 4. Academic Years for School 1
        $ay1 = AcademicYear::firstOrCreate(
            ['name' => '2026-2027', 'school_id' => $school1->id],
            [
                'start_date' => '2026-08-01',
                'end_date' => '2027-06-30',
                'is_current' => true,
                'status' => 'active',
            ]
        );

        // 5. Departments
        $deptMath = Department::firstOrCreate(
            ['code' => 'MATH', 'school_id' => $school1->id],
            [
                'name' => 'Mathematics & Statistics',
                'description' => 'Department of Pure and Applied Mathematics',
                'status' => 'active',
            ]
        );

        $deptSci = Department::firstOrCreate(
            ['code' => 'SCI', 'school_id' => $school1->id],
            [
                'name' => 'Natural Sciences & Technology',
                'description' => 'Physics, Chemistry, and Biological Sciences',
                'status' => 'active',
            ]
        );

        // 6. Classes
        $class9 = SchoolClass::firstOrCreate(
            ['code' => 'G9', 'department_id' => $deptMath->id],
            [
                'name' => 'Grade 9',
                'year_level' => 9,
                'status' => 'active',
            ]
        );

        $class10 = SchoolClass::firstOrCreate(
            ['code' => 'G10', 'department_id' => $deptSci->id],
            [
                'name' => 'Grade 10',
                'year_level' => 10,
                'status' => 'active',
            ]
        );

        // 7. Divisions
        Division::firstOrCreate(
            ['code' => 'G9-A', 'class_id' => $class9->id],
            [
                'name' => 'Section A',
                'capacity' => 35,
                'status' => 'active',
            ]
        );

        Division::firstOrCreate(
            ['code' => 'G9-B', 'class_id' => $class9->id],
            [
                'name' => 'Section B',
                'capacity' => 35,
                'status' => 'active',
            ]
        );

        // 8. Subjects
        Subject::firstOrCreate(
            ['code' => 'MATH-101', 'department_id' => $deptMath->id],
            [
                'name' => 'Algebra & Geometry',
                'credits' => 4,
                'type' => 'core',
                'status' => 'active',
            ]
        );

        Subject::firstOrCreate(
            ['code' => 'PHYS-201', 'department_id' => $deptSci->id],
            [
                'name' => 'General Physics I',
                'credits' => 3,
                'type' => 'core',
                'status' => 'active',
            ]
        );
    }
}
