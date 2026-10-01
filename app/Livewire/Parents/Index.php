<?php

namespace App\Livewire\Parents;

use App\Core\Services\AuditLogger;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\Student;
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
    public string $statusFilter = '';
    public int $perPage = 10;
    public bool $showCreateModal = false;

    // New Parent Form Fields
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $occupation = '';
    public string $employer = '';
    public string $address_line1 = '';
    public string $city = '';
    public string $state = '';
    public string $postal_code = '';
    public string $status = 'active';
    public bool $create_portal_user = true;
    public array $selected_student_ids = [];

    protected function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:parents,email',
            'phone' => 'required|string|max:20',
            'occupation' => 'nullable|string|max:100',
            'address_line1' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function createParent(): void
    {
        $this->authorize('create', ParentProfile::class);
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function resetForm(): void
    {
        $this->reset([
            'first_name', 'middle_name', 'last_name', 'email', 'phone',
            'occupation', 'employer', 'address_line1', 'city', 'state',
            'postal_code', 'status', 'create_portal_user', 'selected_student_ids'
        ]);
        $this->resetValidation();
    }

    public function saveParent(): void
    {
        $this->authorize('create', ParentProfile::class);
        $this->validate();

        $schoolId = Auth::user()->school_id ?? School::first()->id;

        $userId = null;
        if ($this->create_portal_user) {
            $user = User::firstOrCreate(
                ['email' => $this->email],
                [
                    'school_id' => $schoolId,
                    'name' => trim("{$this->first_name} {$this->last_name}"),
                    'phone' => $this->phone,
                    'password' => Hash::make('password'),
                    'status' => 'active',
                ]
            );
            $user->assignRole('parent');
            $userId = $user->id;
        }

        $parentCode = 'PAR-' . date('Y') . '-' . sprintf('%03d', ParentProfile::count() + 1);

        $parent = ParentProfile::create([
            'school_id' => $schoolId,
            'user_id' => $userId,
            'parent_code' => $parentCode,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'occupation' => $this->occupation,
            'employer' => $this->employer,
            'address_line1' => $this->address_line1,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'status' => $this->status,
        ]);

        if (! empty($this->selected_student_ids)) {
            $parent->students()->sync($this->selected_student_ids);
        }

        app(AuditLogger::class)->log(
            action: 'parent.created',
            entityType: ParentProfile::class,
            entityId: $parent->id,
            payload: ['parent_code' => $parentCode, 'name' => $parent->full_name]
        );

        $this->showCreateModal = false;
        $this->resetForm();
        session()->flash('message', "Parent profile {$parent->full_name} created successfully.");
    }

    public function render()
    {
        $this->authorize('viewAny', ParentProfile::class);

        $query = ParentProfile::query()->with(['students', 'user']);

        if (! Auth::user()->hasRole('super-admin') && Auth::user()->school_id) {
            $query->where('school_id', Auth::user()->school_id);
        }

        if (! empty($this->search)) {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('parent_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('students', function ($sQuery) use ($search) {
                        $sQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('admission_number', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        $parents = $query->latest()->paginate($this->perPage);
        $allStudents = Student::select('id', 'first_name', 'last_name', 'admission_number')->get();

        return view('livewire.parents.index', [
            'parents' => $parents,
            'allStudents' => $allStudents,
        ])->layout('components.layouts.admin', ['title' => 'Parent & Family Directory']);
    }
}
