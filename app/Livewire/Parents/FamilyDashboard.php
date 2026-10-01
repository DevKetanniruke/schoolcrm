<?php

namespace App\Livewire\Parents;

use App\Models\ParentProfile;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FamilyDashboard extends Component
{
    use AuthorizesRequests;

    public ?ParentProfile $selectedParent = null;
    public ?Student $selectedChild = null;
    public array $linkedChildren = [];

    public function mount(?int $parent_id = null, ?int $student_id = null)
    {
        $user = Auth::user();

        if ($user->hasRole('parent')) {
            // Authorized parent user: load their own parent profile
            $this->selectedParent = ParentProfile::where('user_id', $user->id)
                ->with(['students.schoolClass', 'students.division', 'students.notes', 'students.documents'])
                ->first();
        } elseif ($parent_id) {
            // Admin user specifying a parent_id
            $this->selectedParent = ParentProfile::with(['students.schoolClass', 'students.division', 'students.notes', 'students.documents'])
                ->find($parent_id);
        } else {
            // Default first parent
            $this->selectedParent = ParentProfile::with(['students.schoolClass', 'students.division', 'students.notes', 'students.documents'])
                ->first();
        }

        if ($this->selectedParent) {
            $this->authorize('view', $this->selectedParent);
            $this->linkedChildren = $this->selectedParent->students->all();

            if ($student_id) {
                $this->selectChild($student_id);
            } elseif (! empty($this->linkedChildren)) {
                $this->selectedChild = $this->linkedChildren[0];
            }
        }
    }

    public function selectChild(int $studentId): void
    {
        if ($this->selectedParent) {
            $this->authorize('switchChild', [$this->selectedParent, $studentId]);

            $child = collect($this->linkedChildren)->firstWhere('id', $studentId);
            if ($child) {
                $this->selectedChild = $child;
            }
        }
    }

    public function render()
    {
        if ($this->selectedParent) {
            $this->selectedParent->loadMissing([
                'students.schoolClass',
                'students.division',
                'students.notes',
                'students.documents',
            ]);
            $this->linkedChildren = $this->selectedParent->students->all();

            if (! $this->selectedChild && ! empty($this->linkedChildren)) {
                $this->selectedChild = $this->linkedChildren[0];
            }
        }

        return view('livewire.parents.family-dashboard', [
            'parent' => $this->selectedParent,
            'selectedChild' => $this->selectedChild,
            'children' => $this->linkedChildren,
        ])->layout('components.layouts.admin', ['title' => 'Family Dashboard & Child Profile Switcher']);
    }
}
