<?php

namespace App\Livewire\Students;

use App\Core\Services\AuditLogger;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\StudentNote;
use App\Models\StudentDocument;
use App\Models\StudentStatusHistory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Show360 extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public Student $student;
    public string $activeTab = 'overview';

    // Note form fields
    public string $noteContent = '';
    public string $noteType = 'general';
    public bool $isConfidential = false;
    public bool $showNoteModal = false;

    // Guardian form fields
    public bool $showGuardianModal = false;
    public string $guardianFirstName = '';
    public string $guardianLastName = '';
    public string $guardianRelationship = 'father';
    public string $guardianPhone = '';
    public string $guardianEmail = '';
    public string $guardianOccupation = '';
    public bool $isPrimaryGuardian = false;
    public bool $isEmergencyContact = true;
    public bool $hasPortalAccess = false;

    // Document form fields
    public bool $showDocumentModal = false;
    public string $documentTitle = '';
    public string $documentType = 'other';
    public $documentFile;

    // Status change fields
    public bool $showStatusModal = false;
    public string $newStatus = '';
    public string $statusReason = '';
    public string $statusNotes = '';

    protected function rules(): array
    {
        return [
            'noteContent' => 'required|string|min:3',
            'noteType' => 'required|in:general,academic,behavioral,medical,counseling,financial',
        ];
    }

    public function mount(Student $student)
    {
        $this->authorize('view', $student);
        $this->student = $student->load([
            'school',
            'academicYear',
            'schoolClass',
            'division',
            'contacts',
            'addresses',
            'guardians',
            'documents.uploader',
            'notes.author',
            'tags',
            'statusHistories.changedBy',
        ]);
        $this->newStatus = $student->status;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function addNote(): void
    {
        $this->authorize('edit', $this->student);

        $this->validate([
            'noteContent' => 'required|string|min:3',
            'noteType' => 'required|string',
        ]);

        $note = $this->student->notes()->create([
            'author_id' => Auth::id(),
            'category' => $this->noteType,
            'note' => $this->noteContent,
            'is_confidential' => $this->isConfidential,
        ]);

        // Log audit activity
        app(AuditLogger::class)->log(
            action: 'student.note_added',
            entityType: Student::class,
            entityId: $this->student->id,
            payload: ['note_id' => $note->id, 'type' => $this->noteType, 'is_confidential' => $this->isConfidential]
        );

        $this->reset(['noteContent', 'noteType', 'isConfidential', 'showNoteModal']);
        $this->student->load('notes.author');
        session()->flash('message', 'Note added successfully.');
    }

    public function addGuardian(): void
    {
        $this->authorize('edit', $this->student);

        $this->validate([
            'guardianFirstName' => 'required|string|max:100',
            'guardianLastName' => 'required|string|max:100',
            'guardianPhone' => 'required|string|max:20',
            'guardianEmail' => 'nullable|email|max:150',
            'guardianRelationship' => 'required|string',
        ]);

        if ($this->isPrimaryGuardian) {
            // Reset existing primary guardian
            $this->student->guardians()->update(['is_primary_contact' => false]);
        }

        $this->student->guardians()->create([
            'name' => trim("{$this->guardianFirstName} {$this->guardianLastName}"),
            'relationship' => $this->guardianRelationship,
            'phone' => $this->guardianPhone,
            'email' => $this->guardianEmail,
            'occupation' => $this->guardianOccupation,
            'is_primary_contact' => $this->isPrimaryGuardian,
            'is_emergency_contact' => $this->isEmergencyContact,
        ]);

        app(AuditLogger::class)->log(
            action: 'student.guardian_added',
            entityType: Student::class,
            entityId: $this->student->id,
            payload: ['guardian_name' => "{$this->guardianFirstName} {$this->guardianLastName}"]
        );

        $this->reset([
            'guardianFirstName', 'guardianLastName', 'guardianPhone',
            'guardianEmail', 'guardianOccupation', 'isPrimaryGuardian',
            'isEmergencyContact', 'hasPortalAccess', 'showGuardianModal'
        ]);
        $this->student->load('guardians');
        session()->flash('message', 'Guardian added successfully.');
    }

    public function uploadDocument(): void
    {
        $this->authorize('edit', $this->student);

        $this->validate([
            'documentTitle' => 'required|string|max:255',
            'documentType' => 'required|string',
            'documentFile' => 'required|file|max:10240', // 10MB
        ]);

        $filePath = $this->documentFile->store('student_documents/' . $this->student->id, 'public');
        $fileSize = $this->documentFile->getSize();

        $this->student->documents()->create([
            'title' => $this->documentTitle,
            'document_type' => $this->documentType,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'uploaded_by' => Auth::id(),
        ]);

        app(AuditLogger::class)->log(
            action: 'student.document_uploaded',
            entityType: Student::class,
            entityId: $this->student->id,
            payload: ['title' => $this->documentTitle]
        );

        $this->reset(['documentTitle', 'documentType', 'documentFile', 'showDocumentModal']);
        $this->student->load('documents.uploader');
        session()->flash('message', 'Document uploaded successfully.');
    }

    public function updateStatus(): void
    {
        $this->authorize('edit', $this->student);

        $this->validate([
            'newStatus' => 'required|in:active,inactive,suspended,transferred,graduated',
            'statusReason' => 'required|string|max:255',
        ]);

        $oldStatus = $this->student->status;

        if ($oldStatus !== $this->newStatus) {
            $this->student->update(['status' => $this->newStatus]);

            $this->student->statusHistories()->create([
                'from_status' => $oldStatus,
                'to_status' => $this->newStatus,
                'reason' => $this->statusReason,
                'changed_by' => Auth::id(),
            ]);

            app(AuditLogger::class)->log(
                action: 'student.status_updated',
                entityType: Student::class,
                entityId: $this->student->id,
                payload: ['from' => $oldStatus, 'to' => $this->newStatus, 'reason' => $this->statusReason]
            );
        }

        $this->reset(['statusReason', 'statusNotes', 'showStatusModal']);
        $this->student->load('statusHistories.changedBy');
        session()->flash('message', 'Student status updated successfully.');
    }

    public function getTimelineProperty()
    {
        // Aggregate status updates, notes, documents, and guardians into a unified chronological activity timeline
        $timeline = collect();

        foreach ($this->student->statusHistories as $sh) {
            $timeline->push([
                'type' => 'status_change',
                'title' => "Status updated to " . ucfirst($sh->to_status),
                'description' => "Reason: " . ($sh->reason ?? 'N/A'),
                'author' => $sh->changedBy ? $sh->changedBy->name : 'System',
                'timestamp' => $sh->created_at,
                'icon' => 'status',
                'color' => 'amber',
            ]);
        }

        $canViewConfidential = Auth::user()->can('viewConfidentialNotes', $this->student);

        foreach ($this->student->notes as $note) {
            if ($note->is_confidential && !$canViewConfidential) {
                continue;
            }
            $timeline->push([
                'type' => 'note',
                'title' => "Note added: " . ucfirst($note->category) . ($note->is_confidential ? ' (Confidential)' : ''),
                'description' => $note->note,
                'author' => $note->author ? $note->author->name : 'System',
                'timestamp' => $note->created_at,
                'icon' => 'note',
                'color' => $note->is_confidential ? 'rose' : 'indigo',
            ]);
        }

        foreach ($this->student->documents as $doc) {
            $timeline->push([
                'type' => 'document',
                'title' => "Document uploaded: " . $doc->title,
                'description' => "Type: " . ucfirst($doc->document_type),
                'author' => $doc->uploader ? $doc->uploader->name : 'System',
                'timestamp' => $doc->created_at,
                'icon' => 'document',
                'color' => 'emerald',
            ]);
        }

        foreach ($this->student->guardians as $g) {
            $timeline->push([
                'type' => 'guardian',
                'title' => "Guardian added: " . $g->name,
                'description' => "Relationship: " . ucfirst($g->relationship) . ($g->is_primary_contact ? ' (Primary)' : ''),
                'author' => 'Admin',
                'timestamp' => $g->created_at,
                'icon' => 'user',
                'color' => 'blue',
            ]);
        }

        return $timeline->sortByDesc('timestamp')->values();
    }

    public function render()
    {
        $canViewConfidentialNotes = Auth::user()->can('viewConfidentialNotes', $this->student);

        return view('livewire.students.show360', [
            'timeline' => $this->timeline,
            'canViewConfidentialNotes' => $canViewConfidentialNotes,
        ])->layout('components.layouts.admin', ['title' => "Student 360° - {$this->student->full_name}"]);
    }
}
