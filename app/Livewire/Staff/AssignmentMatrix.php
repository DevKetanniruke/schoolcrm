<?php

namespace App\Livewire\Staff;

use App\Models\Department;
use App\Models\SchoolClass;
use App\Models\StaffProfile;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AssignmentMatrix extends Component
{
    use AuthorizesRequests;

    public string $department_id = '';
    public string $search = '';

    public function render()
    {
        $this->authorize('viewAny', StaffProfile::class);

        $currentUser = Auth::user();

        $query = StaffProfile::with(['department', 'subjects', 'classes.divisions', 'classAssignments']);

        if ($currentUser->school_id && ! $currentUser->hasRole('super-admin')) {
            $query->where('school_id', $currentUser->school_id);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('employee_code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('subjects', function ($sq) {
                        $sq->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->department_id !== '') {
            $query->where('department_id', $this->department_id);
        }

        $teachers = $query->where('role_type', 'teacher')->orderBy('first_name')->get();
        $departments = Department::orderBy('name')->get();
        $schoolClasses = SchoolClass::orderBy('year_level')->get();

        return view('livewire.staff.assignment-matrix', [
            'teachers' => $teachers,
            'departments' => $departments,
            'schoolClasses' => $schoolClasses,
        ])->layout('components.layouts.admin', ['title' => 'Teacher Class & Subject Assignment Matrix']);
    }
}
