<?php

namespace App\Livewire\Parents;

use App\Core\Services\AuditLogger;
use App\Models\ParentAppointment;
use App\Models\ParentCommunication;
use App\Models\ParentComplaint;
use App\Models\ParentDocument;
use App\Models\ParentNote;
use App\Models\ParentProfile;
use App\Models\ParentTask;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show360 extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public ParentProfile $parent;
    public string $activeTab = 'overview';

    // Communication form fields
    public bool $showCommModal = false;
    public string $commType = 'phone_call';
    public string $commDirection = 'outbound';
    public string $commSubject = '';
    public string $commSummary = '';
    public ?int $commStudentId = null;

    // Complaint form fields
    public bool $showComplaintModal = false;
    public string $complaintCategory = 'general';
    public string $complaintPriority = 'medium';
    public string $complaintSubject = '';
    public string $complaintDescription = '';
    public ?int $complaintStudentId = null;

    // Appointment form fields
    public bool $showApptModal = false;
    public string $apptType = 'parent_teacher_meeting';
    public string $apptTitle = '';
    public string $scheduledAt = '';
    public int $durationMinutes = 30;
    public string $locationLink = '';
    public ?int $apptStudentId = null;

    // Task form fields
    public bool $showTaskModal = false;
    public string $taskTitle = '';
    public string $taskDescription = '';
    public string $dueDate = '';
    public string $taskPriority = 'medium';
    public ?int $taskStudentId = null;

    // Note form fields
    public bool $showNoteModal = false;
    public string $noteContent = '';
    public string $noteCategory = 'general';
    public bool $isConfidential = false;

    // Document form fields
    public bool $showDocModal = false;
    public string $docTitle = '';
    public string $docType = 'other';
    public $docFile;
    public ?int $docStudentId = null;

    // Link Student form
    public bool $showLinkStudentModal = false;
    public ?int $linkStudentId = null;
    public string $linkRelationship = 'mother';

    public function mount(ParentProfile $parent)
    {
        $this->authorize('view', $parent);

        $this->parent = $parent->load([
            'school',
            'user',
            'students.schoolClass',
            'students.division',
            'communications.staff',
            'communications.student',
            'complaints.assignedStaff',
            'complaints.student',
            'appointments.staff',
            'appointments.student',
            'tasks.assigner',
            'tasks.student',
            'notes.author',
            'documents.uploader',
            'documents.student',
            'familyMembers',
        ]);

        $this->scheduledAt = now()->addDays(1)->format('Y-m-d\TH:i');
        $this->dueDate = now()->addDays(7)->format('Y-m-d');
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function addCommunication(): void
    {
        $this->authorize('update', $this->parent);

        $this->validate([
            'commSubject' => 'required|string|max:255',
            'commSummary' => 'required|string|min:3',
        ]);

        $comm = $this->parent->communications()->create([
            'school_id' => $this->parent->school_id,
            'student_id' => $this->commStudentId,
            'staff_id' => Auth::id(),
            'type' => $this->commType,
            'direction' => $this->commDirection,
            'subject' => $this->commSubject,
            'summary' => $this->commSummary,
            'interaction_date' => now(),
            'status' => 'completed',
        ]);

        app(AuditLogger::class)->log(
            action: 'parent.communication_added',
            entityType: ParentProfile::class,
            entityId: $this->parent->id,
            payload: ['subject' => $this->commSubject, 'type' => $this->commType]
        );

        $this->reset(['commSubject', 'commSummary', 'commStudentId', 'showCommModal']);
        $this->parent->load('communications.staff');
        session()->flash('message', 'Communication entry logged successfully.');
    }

    public function addComplaint(): void
    {
        $this->authorize('update', $this->parent);

        $this->validate([
            'complaintSubject' => 'required|string|max:255',
            'complaintDescription' => 'required|string|min:5',
        ]);

        $ticketNum = 'CMP-' . date('Y') . '-' . sprintf('%03d', ParentComplaint::count() + 1);

        $this->parent->complaints()->create([
            'school_id' => $this->parent->school_id,
            'student_id' => $this->complaintStudentId,
            'ticket_number' => $ticketNum,
            'category' => $this->complaintCategory,
            'priority' => $this->complaintPriority,
            'status' => 'open',
            'subject' => $this->complaintSubject,
            'description' => $this->complaintDescription,
        ]);

        app(AuditLogger::class)->log(
            action: 'parent.complaint_logged',
            entityType: ParentProfile::class,
            entityId: $this->parent->id,
            payload: ['ticket_number' => $ticketNum]
        );

        $this->reset(['complaintSubject', 'complaintDescription', 'complaintStudentId', 'showComplaintModal']);
        $this->parent->load('complaints');
        session()->flash('message', "Complaint ticket {$ticketNum} logged successfully.");
    }

    public function scheduleAppointment(): void
    {
        $this->authorize('update', $this->parent);

        $this->validate([
            'apptTitle' => 'required|string|max:255',
            'scheduledAt' => 'required|date',
        ]);

        $this->parent->appointments()->create([
            'school_id' => $this->parent->school_id,
            'student_id' => $this->apptStudentId,
            'staff_id' => Auth::id(),
            'type' => $this->apptType,
            'title' => $this->apptTitle,
            'scheduled_at' => $this->scheduledAt,
            'duration_minutes' => $this->durationMinutes,
            'location_or_link' => $this->locationLink,
            'status' => 'scheduled',
        ]);

        app(AuditLogger::class)->log(
            action: 'parent.appointment_scheduled',
            entityType: ParentProfile::class,
            entityId: $this->parent->id,
            payload: ['title' => $this->apptTitle, 'time' => $this->scheduledAt]
        );

        $this->reset(['apptTitle', 'locationLink', 'apptStudentId', 'showApptModal']);
        $this->parent->load('appointments.staff');
        session()->flash('message', 'Appointment scheduled successfully.');
    }

    public function addTask(): void
    {
        $this->authorize('update', $this->parent);

        $this->validate([
            'taskTitle' => 'required|string|max:255',
        ]);

        $this->parent->tasks()->create([
            'school_id' => $this->parent->school_id,
            'student_id' => $this->taskStudentId,
            'assigned_by' => Auth::id(),
            'title' => $this->taskTitle,
            'description' => $this->taskDescription,
            'due_date' => $this->dueDate ?: null,
            'priority' => $this->taskPriority,
            'status' => 'pending',
        ]);

        $this->reset(['taskTitle', 'taskDescription', 'taskStudentId', 'showTaskModal']);
        $this->parent->load('tasks');
        session()->flash('message', 'Task assigned successfully.');
    }

    public function addNote(): void
    {
        $this->authorize('update', $this->parent);

        $this->validate([
            'noteContent' => 'required|string|min:3',
        ]);

        $this->parent->notes()->create([
            'author_id' => Auth::id(),
            'category' => $this->noteCategory,
            'note' => $this->noteContent,
            'is_confidential' => $this->isConfidential,
        ]);

        $this->reset(['noteContent', 'noteCategory', 'isConfidential', 'showNoteModal']);
        $this->parent->load('notes.author');
        session()->flash('message', 'Note added successfully.');
    }

    public function uploadDocument(): void
    {
        $this->authorize('update', $this->parent);

        $this->validate([
            'docTitle' => 'required|string|max:255',
            'docFile' => 'required|file|max:10240',
        ]);

        $path = $this->docFile->store('parent_documents/' . $this->parent->id, 'public');
        $size = $this->docFile->getSize();
        $mime = $this->docFile->getMimeType();

        $this->parent->documents()->create([
            'student_id' => $this->docStudentId,
            'title' => $this->docTitle,
            'document_type' => $this->docType,
            'file_path' => $path,
            'file_size' => $size,
            'mime_type' => $mime,
            'uploaded_by' => Auth::id(),
        ]);

        $this->reset(['docTitle', 'docFile', 'docStudentId', 'showDocModal']);
        $this->parent->load('documents.uploader');
        session()->flash('message', 'Document uploaded successfully.');
    }

    public function linkStudent(): void
    {
        $this->authorize('update', $this->parent);

        $this->validate([
            'linkStudentId' => 'required|exists:students,id',
            'linkRelationship' => 'required|string',
        ]);

        $this->parent->students()->syncWithoutDetaching([
            $this->linkStudentId => [
                'relationship_type' => $this->linkRelationship,
                'is_primary_contact' => true,
                'is_emergency_contact' => true,
                'has_custody' => true,
                'financial_responsibility' => true,
                'portal_access_enabled' => true,
            ],
        ]);

        $this->reset(['linkStudentId', 'showLinkStudentModal']);
        $this->parent->load('students');
        session()->flash('message', 'Child linked successfully to parent profile.');
    }

    public function render()
    {
        $this->parent->loadMissing([
            'school',
            'user',
            'students.schoolClass',
            'students.division',
            'communications.staff',
            'communications.student',
            'complaints.assignedStaff',
            'complaints.student',
            'appointments.staff',
            'appointments.student',
            'tasks.assigner',
            'tasks.student',
            'notes.author',
            'documents.uploader',
            'documents.student',
            'familyMembers',
        ]);

        $canViewConfidentialNotes = Auth::user()->can('viewConfidentialNotes', $this->parent);
        $allStudents = Student::select('id', 'first_name', 'last_name', 'admission_number')->get();

        return view('livewire.parents.show360', [
            'canViewConfidentialNotes' => $canViewConfidentialNotes,
            'allStudents' => $allStudents,
        ])->layout('components.layouts.admin', ['title' => "Parent 360° - {$this->parent->full_name}"]);
    }
}
