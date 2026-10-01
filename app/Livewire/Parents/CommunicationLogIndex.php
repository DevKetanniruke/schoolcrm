<?php

namespace App\Livewire\Parents;

use App\Models\ParentCommunication;
use App\Models\ParentProfile;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CommunicationLogIndex extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $search = '';
    public string $typeFilter = '';
    public string $directionFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public ?int $parentFilter = null;
    public ?int $studentFilter = null;
    public int $perPage = 15;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDirectionFilter(): void
    {
        $this->resetPage();
    }

    public function updatedParentFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStudentFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'typeFilter', 'directionFilter', 'dateFrom', 'dateTo', 'parentFilter', 'studentFilter']);
        $this->resetPage();
    }

    public function render()
    {
        $this->authorize('viewAny', ParentProfile::class);

        $query = ParentCommunication::query()->with(['parent', 'student', 'staff']);

        if (! Auth::user()->hasRole('super-admin') && Auth::user()->school_id) {
            $query->where('school_id', Auth::user()->school_id);
        }

        if (Auth::user()->hasRole('parent')) {
            $parentProfile = ParentProfile::where('user_id', Auth::id())->first();
            if ($parentProfile) {
                $query->where('parent_id', $parentProfile->id);
            }
        }

        if (! empty($this->search)) {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%")
                    ->orWhereHas('parent', function ($pQuery) use ($search) {
                        $pQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('student', function ($sQuery) use ($search) {
                        $sQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($this->typeFilter)) {
            $query->where('type', $this->typeFilter);
        }

        if (! empty($this->directionFilter)) {
            $query->where('direction', $this->directionFilter);
        }

        if (! empty($this->parentFilter)) {
            $query->where('parent_id', $this->parentFilter);
        }

        if (! empty($this->studentFilter)) {
            $query->where('student_id', $this->studentFilter);
        }

        if (! empty($this->dateFrom)) {
            $query->whereDate('interaction_date', '>=', $this->dateFrom);
        }

        if (! empty($this->dateTo)) {
            $query->whereDate('interaction_date', '<=', $this->dateTo);
        }

        $communications = $query->orderBy('interaction_date', 'desc')->paginate($this->perPage);
        $parentsList = ParentProfile::select('id', 'first_name', 'last_name', 'email')->get();
        $studentsList = Student::select('id', 'first_name', 'last_name', 'admission_number')->get();

        return view('livewire.parents.communication-log-index', [
            'communications' => $communications,
            'parentsList' => $parentsList,
            'studentsList' => $studentsList,
        ])->layout('components.layouts.admin', ['title' => 'Parent Communication Log Center']);
    }
}
