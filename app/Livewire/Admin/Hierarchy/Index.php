<?php

namespace App\Livewire\Admin\Hierarchy;

use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Division;
use App\Models\Organization;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Index extends Component
{
    use AuthorizesRequests;

    public string $activeTab = 'organizations';

    public function selectTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    #[Layout('components.layouts.admin')]
    public function render()
    {
        $organizations = Organization::withCount('schools')->get();
        $schools = School::with(['organization'])->withCount(['campuses', 'departments'])->get();
        $campuses = Campus::with('school')->get();
        $academicYears = AcademicYear::with('school')->get();
        $departments = Department::with('school')->withCount(['classes', 'subjects'])->get();
        $classes = SchoolClass::with('department.school')->withCount('divisions')->get();
        $divisions = Division::with('schoolClass.department.school')->get();
        $subjects = Subject::with('department.school')->get();

        return view('livewire.admin.hierarchy.index', [
            'organizations' => $organizations,
            'schools' => $schools,
            'campuses' => $campuses,
            'academicYears' => $academicYears,
            'departments' => $departments,
            'classes' => $classes,
            'divisions' => $divisions,
            'subjects' => $subjects,
        ]);
    }
}
