<?php

namespace App\Livewire\Admin;

use App\Core\Services\DigitalDocumentService;
use App\Models\AdmissionApplication;
use App\Models\AdmissionLead;
use App\Models\DigitalDocument;
use App\Models\DocumentActivityLog;
use App\Models\DocumentRetentionPolicy;
use App\Models\ParentComplaint;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\StaffProfile;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DocumentVault extends Component
{
    use AuthorizesRequests, WithFileUploads, WithPagination;

    // Filters
    public string $search = '';
    public string $categoryFilter = '';
    public string $statusFilter = '';
    public string $ownerTypeFilter = '';
    public string $confidentialFilter = '';

    // Upload Modal State
    public bool $showUploadModal = false;
    public string $title = '';
    public string $category = 'general';
    public ?string $documentable_type = null;
    public ?int $documentable_id = null;
    public ?string $expiry_date = null;
    public bool $is_confidential = false;
    public $file;

    // Version Modal State
    public bool $showVersionModal = false;
    public ?int $selectedDocumentId = null;
    public $newVersionFile;
    public string $versionNotes = '';

    // Verification Modal State
    public bool $showVerificationModal = false;
    public string $verificationStatus = 'verified';
    public string $verificationNotes = '';

    // Retention Policy Modal State
    public bool $showPolicyModal = false;
    public string $policyCategory = 'general';
    public int $policyMonths = 36;
    public bool $policyAutoArchive = true;
    public bool $policyAutoDelete = false;

    // Detail/Audit Modal State
    public bool $showAuditModal = false;
    public ?DigitalDocument $activeDocument = null;

    public array $categories = [
        'general' => 'General Document',
        'birth_certificate' => 'Birth Certificate',
        'id_proof' => 'Identity Proof (Passport / Aadhar / SSN)',
        'academic_transcript' => 'Academic Transcript & Report Card',
        'medical_record' => 'Medical & Health Record',
        'employment_contract' => 'Teacher Employment Contract',
        'application_form' => 'Admission Application Form',
        'complaint_evidence' => 'Parent Complaint Evidence',
    ];

    public array $ownerTypes = [
        'Student' => 'Student',
        'ParentProfile' => 'Parent',
        'StaffProfile' => 'Teacher / Staff',
        'AdmissionLead' => 'Admission Lead',
        'AdmissionApplication' => 'Application',
        'ParentComplaint' => 'Complaint',
        'School' => 'School Institutional',
    ];

    public function mount()
    {
        // Sample seed check for retention policies if empty
        $user = Auth::user();
        $schoolId = $user?->school_id ?? 1;

        if (DocumentRetentionPolicy::where('school_id', $schoolId)->count() === 0) {
            foreach ($this->categories as $catKey => $catName) {
                DocumentRetentionPolicy::create([
                    'school_id' => $schoolId,
                    'document_category' => $catKey,
                    'retention_period_months' => match ($catKey) {
                        'birth_certificate', 'id_proof' => 120,
                        'academic_transcript' => 60,
                        default => 36,
                    },
                    'auto_archive' => true,
                    'auto_delete' => false,
                ]);
            }
        }
    }

    public function openUploadModal()
    {
        $this->reset(['title', 'category', 'documentable_type', 'documentable_id', 'expiry_date', 'is_confidential', 'file']);
        $this->category = 'general';
        $this->showUploadModal = true;
    }

    public function storeDocument(DigitalDocumentService $documentService)
    {
        $this->validate([
            'title' => 'required|string|min:3|max:150',
            'category' => 'required|string',
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $user = Auth::user();
        $schoolId = $user?->school_id ?? 1;

        $ownerClass = null;
        if ($this->documentable_type) {
            $ownerClass = "App\\Models\\" . $this->documentable_type;
        }

        $documentService->storeDocument([
            'school_id' => $schoolId,
            'documentable_type' => $ownerClass,
            'documentable_id' => $this->documentable_id ?: null,
            'title' => $this->title,
            'category' => $this->category,
            'expiry_date' => $this->expiry_date,
            'is_confidential' => $this->is_confidential,
        ], $this->file);

        $this->showUploadModal = false;
        $this->reset(['title', 'category', 'documentable_type', 'documentable_id', 'expiry_date', 'is_confidential', 'file']);

        $this->dispatch('notify', message: 'Digital document securely stored in vault.');
    }

    public function openVersionModal(int $documentId)
    {
        $this->selectedDocumentId = $documentId;
        $this->reset(['newVersionFile', 'versionNotes']);
        $this->showVersionModal = true;
    }

    public function storeNewVersion(DigitalDocumentService $documentService)
    {
        $this->validate([
            'newVersionFile' => 'required|file|max:10240',
        ]);

        $document = DigitalDocument::findOrFail($this->selectedDocumentId);
        $documentService->addVersion($document, $this->newVersionFile, $this->versionNotes);

        $this->showVersionModal = false;
        $this->reset(['newVersionFile', 'versionNotes', 'selectedDocumentId']);

        $this->dispatch('notify', message: 'New document version successfully uploaded and archived.');
    }

    public function openVerificationModal(int $documentId)
    {
        $this->selectedDocumentId = $documentId;
        $document = DigitalDocument::findOrFail($documentId);
        $this->verificationStatus = $document->verification_status === 'pending' ? 'verified' : $document->verification_status;
        $this->verificationNotes = $document->verification_notes ?? '';
        $this->showVerificationModal = true;
    }

    public function updateVerification(DigitalDocumentService $documentService)
    {
        $document = DigitalDocument::findOrFail($this->selectedDocumentId);
        $documentService->updateVerificationStatus($document, $this->verificationStatus, $this->verificationNotes);

        $this->showVerificationModal = false;
        $this->reset(['selectedDocumentId', 'verificationStatus', 'verificationNotes']);

        $this->dispatch('notify', message: 'Document verification status updated.');
    }

    public function openAuditModal(int $documentId)
    {
        $this->activeDocument = DigitalDocument::with(['uploader', 'verifier', 'versions.uploader', 'activityLogs.user'])->findOrFail($documentId);
        $this->showAuditModal = true;
    }

    public function openPolicyModal()
    {
        $this->showPolicyModal = true;
    }

    public function saveRetentionPolicy(DigitalDocumentService $documentService)
    {
        $user = Auth::user();
        $schoolId = $user?->school_id ?? 1;

        $documentService->updateRetentionPolicy(
            $schoolId,
            $this->policyCategory,
            $this->policyMonths,
            $this->policyAutoArchive,
            $this->policyAutoDelete
        );

        $this->showPolicyModal = false;
        $this->dispatch('notify', message: 'Document retention policy saved.');
    }

    public function render()
    {
        $user = Auth::user();
        $schoolId = $user?->school_id ?? 1;

        $query = DigitalDocument::with(['uploader', 'verifier', 'documentable'])
            ->where('school_id', $schoolId);

        if (! empty($this->search)) {
            $query->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('file_name', 'like', '%' . $this->search . '%');
        }

        if (! empty($this->categoryFilter)) {
            $query->where('category', $this->categoryFilter);
        }

        if (! empty($this->statusFilter)) {
            $query->where('verification_status', $this->statusFilter);
        }

        if (! empty($this->ownerTypeFilter)) {
            $query->where('documentable_type', 'App\\Models\\' . $this->ownerTypeFilter);
        }

        if ($this->confidentialFilter !== '') {
            $query->where('is_confidential', (bool) $this->confidentialFilter);
        }

        $documents = $query->latest()->paginate(12);

        $policies = DocumentRetentionPolicy::where('school_id', $schoolId)->get();

        return view('livewire.admin.document-vault', [
            'documents' => $documents,
            'policies' => $policies,
        ])->layout('components.layouts.admin', ['title' => 'Digital Document Management System']);
    }
}
