<?php

namespace App\Livewire\Students;

use App\Core\Services\AuditLogger;
use App\Models\AcademicYear;
use App\Models\Division;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Rules\PhoneRule;
use App\Rules\SanitizedTextRule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, AuthorizesRequests;

    public string $search = '';
    public string $statusFilter = '';
    public string $classFilter = '';
    public string $divisionFilter = '';

    // Create Modal state
    public bool $showCreateModal = false;

    // Form attributes
    public string $first_name = '';
    public ?string $middle_name = null;
    public string $last_name = '';
    public string $student_id_number = '';
    public string $admission_number = '';
    public string $date_of_birth = '';
    public string $gender = 'male';
    public ?string $email = null;
    public ?string $phone = null;
    public ?int $academic_year_id = null;
    public ?int $class_id = null;
    public ?int $division_id = null;
    public ?string $roll_number = null;
    public string $admission_date = '';
    public string $status = 'active';

    // Guardian details
    public string $guardian_name = '';
    public string $guardian_relationship = 'father';
    public string $guardian_phone = '';
    public ?string $guardian_email = null;

    protected $queryString = ['search', 'statusFilter', 'classFilter', 'divisionFilter'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedClassFilter()
    {
        $this->resetPage();
    }

    public function updatedDivisionFilter()
    {
        $this->resetPage();
    }

    public function createStudent()
    {
        $this->openCreateModal();
    }

    public function openCreateModal()
    {
        $this->authorize('create', Student::class);

        $schoolId = auth()->user()->school_id;
        $this->reset(['first_name', 'middle_name', 'last_name', 'email', 'phone', 'roll_number', 'guardian_name', 'guardian_phone', 'guardian_email']);
        $this->gender = 'male';
        $this->status = 'active';
        $this->admission_date = now()->format('Y-m-d');
        $this->date_of_birth = '2012-01-01';

        // Auto-generate ID & Admission numbers
        $rand = rand(100, 999);
        $this->student_id_number = 'STU-' . date('Y') . '-' . $rand;
        $this->admission_number = 'ADM-' . rand(1000, 9999);

        $ay = AcademicYear::where('school_id', $schoolId)->where('is_current', true)->first();
        $this->academic_year_id = $ay->id ?? null;

        $cls = SchoolClass::first();
        $this->class_id = $cls->id ?? null;

        $div = Division::first();
        $this->division_id = $div->id ?? null;

        $this->showCreateModal = true;
        $this->dispatch('open-drawer', 'student-drawer');
    }

    public function saveStudent(AuditLogger $auditLogger)
    {
        $this->authorize('create', Student::class);

        $this->validate([
            'first_name' => ['required', 'string', 'max:100', new SanitizedTextRule],
            'last_name' => ['required', 'string', 'max:100', new SanitizedTextRule],
            'student_id_number' => ['required', 'string', 'max:50', 'unique:students,student_id_number'],
            'admission_number' => ['required', 'string', 'max:50', 'unique:students,admission_number'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', 'in:male,female,other'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', new PhoneRule],
            'admission_date' => ['required', 'date'],
            'status' => ['required', 'in:active,inactive,suspended,graduated,transferred'],
            'guardian_name' => ['required', 'string', 'max:255', new SanitizedTextRule],
            'guardian_phone' => ['required', 'string', new PhoneRule],
        ]);

        $student = Student::create([
            'school_id' => auth()->user()->school_id,
            'student_id_number' => $this->student_id_number,
            'admission_number' => $this->admission_number,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'email' => $this->email,
            'phone' => $this->phone,
            'academic_year_id' => $this->academic_year_id,
            'class_id' => $this->class_id,
            'division_id' => $this->division_id,
            'roll_number' => $this->roll_number,
            'admission_date' => $this->admission_date,
            'status' => $this->status,
        ]);

        StudentGuardian::create([
            'student_id' => $student->id,
            'name' => $this->guardian_name,
            'relationship' => $this->guardian_relationship,
            'phone' => $this->guardian_phone,
            'email' => $this->guardian_email,
            'is_primary_contact' => true,
            'is_emergency_contact' => true,
        ]);

        $auditLogger->log(
            action: 'student.created',
            payload: ['student_id' => $student->id, 'admission_number' => $student->admission_number]
        );

        $this->showCreateModal = false;
        $this->dispatch('close-drawer', 'student-drawer');

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Student Enrolled',
            'message' => "Student {$student->full_name} enrolled successfully.",
        ]);
    }

    #[Layout('components.layouts.admin')]
    public function render()
    {
        $this->authorize('viewAny', Student::class);

        $query = Student::with(['school', 'academicYear', 'schoolClass', 'division', 'guardians', 'tags']);

        // Multi-attribute Search across: Name, Admission No, Student ID, Parent Phone, Email
        if (! empty($this->search)) {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('admission_number', 'like', "%{$search}%")
                    ->orWhere('student_id_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('guardians', function ($gQuery) use ($search) {
                        $gQuery->where('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (! empty($this->classFilter)) {
            $query->where('class_id', $this->classFilter);
        }

        if (! empty($this->divisionFilter)) {
            $query->where('division_id', $this->divisionFilter);
        }

        $students = $query->latest()->paginate(10);

        $classes = SchoolClass::all();
        $academicYears = AcademicYear::all();
        $divisions = Division::all();

        return view('livewire.students.index', [
            'students' => $students,
            'classes' => $classes,
            'academicYears' => $academicYears,
            'divisions' => $divisions,
        ])->layout('components.layouts.admin', ['title' => 'Student Directory']);
    }
}
