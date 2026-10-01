<?php

namespace App\Livewire\Staff;

use App\Models\Department;
use App\Models\Role;
use App\Models\School;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $search = '';
    public string $department_id = '';
    public string $role_type = '';
    public string $status = '';
    public string $employment_type = '';
    public int $perPage = 10;

    // Create Drawer Form State
    public bool $showCreateModal = false;
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $role_type_input = 'teacher';
    public string $designation = '';
    public string $new_department_id = '';
    public string $qualification = '';
    public int $experience_years = 0;
    public string $joining_date = '';
    public string $employment_type_input = 'full_time';
    public bool $create_user_account = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'department_id' => ['except' => ''],
        'role_type' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function createStaff(): void
    {
        $this->authorize('create', StaffProfile::class);
        $this->resetCreateForm();
        $this->joining_date = now()->format('Y-m-d');
        $this->showCreateModal = true;
    }

    public function resetCreateForm(): void
    {
        $this->reset([
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'phone',
            'designation',
            'new_department_id',
            'qualification',
            'experience_years',
            'joining_date',
        ]);
        $this->role_type_input = 'teacher';
        $this->employment_type_input = 'full_time';
        $this->create_user_account = true;
        $this->resetValidation();
    }

    public function saveStaff(): void
    {
        $this->authorize('create', StaffProfile::class);

        $validated = $this->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:staff_profiles,email',
            'phone' => 'nullable|string|max:30',
            'role_type_input' => 'required|string',
            'designation' => 'nullable|string|max:100',
            'new_department_id' => 'nullable|exists:departments,id',
            'qualification' => 'nullable|string|max:150',
            'experience_years' => 'required|integer|min:0|max:50',
            'joining_date' => 'nullable|date',
            'employment_type_input' => 'required|string',
        ]);

        $currentUser = Auth::user();
        $schoolId = $currentUser->school_id ?? School::first()->id ?? 1;

        $userId = null;
        if ($this->create_user_account) {
            $user = User::create([
                'school_id' => $schoolId,
                'name' => trim("{$this->first_name} {$this->middle_name} {$this->last_name}"),
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => Hash::make('password'),
                'status' => 'active',
            ]);

            $roleSlug = match ($this->role_type_input) {
                'teacher' => 'teacher',
                'counselor' => 'counselor',
                'hr' => 'hr',
                'principal' => 'principal',
                'accountant' => 'accountant',
                default => 'staff',
            };

            $role = Role::where('slug', $roleSlug)->first();
            if ($role) {
                $user->assignRole($role);
            }
            $userId = $user->id;
        }

        // Generate Employee Code: EMP-YYYY-XXX
        $count = StaffProfile::where('school_id', $schoolId)->count() + 1;
        $employeeCode = 'EMP-' . date('Y') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        StaffProfile::create([
            'school_id' => $schoolId,
            'user_id' => $userId,
            'department_id' => $this->new_department_id ?: null,
            'employee_code' => $employeeCode,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role_type' => $this->role_type_input,
            'designation' => $this->designation ?: ucfirst($this->role_type_input),
            'qualification' => $this->qualification,
            'experience_years' => $this->experience_years,
            'joining_date' => $this->joining_date ?: now(),
            'employment_type' => $this->employment_type_input,
            'status' => 'active',
        ]);

        $this->showCreateModal = false;
        $this->resetCreateForm();
        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Staff Profile Created',
            'message' => "Successfully registered {$this->first_name} {$this->last_name} with Employee Code {$employeeCode}.",
        ]);
    }

    public function render()
    {
        $this->authorize('viewAny', StaffProfile::class);

        $currentUser = Auth::user();

        $query = StaffProfile::with(['department', 'subjects', 'classes']);

        if ($currentUser->school_id && ! $currentUser->hasRole('super-admin')) {
            $query->where('school_id', $currentUser->school_id);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('employee_code', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('qualification', 'like', '%' . $this->search . '%')
                    ->orWhere('designation', 'like', '%' . $this->search . '%')
                    ->orWhereHas('subjects', function ($sq) {
                        $sq->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->department_id !== '') {
            $query->where('department_id', $this->department_id);
        }

        if ($this->role_type !== '') {
            $query->where('role_type', $this->role_type);
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->employment_type !== '') {
            $query->where('employment_type', $this->employment_type);
        }

        $staffMembers = $query->orderBy('first_name')->paginate($this->perPage);
        $departments = Department::orderBy('name')->get();

        return view('livewire.staff.index', [
            'staffMembers' => $staffMembers,
            'departments' => $departments,
        ])->layout('components.layouts.admin', ['title' => 'Teacher & Staff Directory']);
    }
}
