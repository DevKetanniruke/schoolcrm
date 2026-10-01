<?php

namespace App\Core\Services;

use App\Models\DigitalDocument;
use App\Models\DocumentActivityLog;
use App\Models\DocumentRetentionPolicy;
use App\Models\DocumentVersion;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DigitalDocumentService
{
    /**
     * Store new digital document securely in private storage.
     */
    public function storeDocument(array $data, UploadedFile $file): DigitalDocument
    {
        $user = Auth::user();
        $schoolId = $data['school_id'] ?? $user?->school_id ?? 1;

        // Store file in private disk directory (outside public root)
        $folderPath = "private_documents/{$schoolId}/" . now()->format('Y/m');
        $storedPath = $file->store($folderPath, 'local');

        // Determine retention until date based on retention policy
        $retentionDate = $this->calculateRetentionDate($schoolId, $data['category'] ?? 'general');

        $document = DigitalDocument::create([
            'school_id' => $schoolId,
            'documentable_type' => $data['documentable_type'] ?? null,
            'documentable_id' => $data['documentable_id'] ?? null,
            'title' => $data['title'] ?? $file->getClientOriginalName(),
            'category' => $data['category'] ?? 'general',
            'file_path' => $storedPath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType() ?: 'application/pdf',
            'version' => 1,
            'expiry_date' => ! empty($data['expiry_date']) ? Carbon::parse($data['expiry_date']) : null,
            'verification_status' => $data['verification_status'] ?? 'pending',
            'retention_until_date' => $retentionDate,
            'is_confidential' => ! empty($data['is_confidential']),
            'uploaded_by' => $user?->id ?? 1,
        ]);

        // Create initial version #1 record
        DocumentVersion::create([
            'document_id' => $document->id,
            'version_number' => 1,
            'file_path' => $storedPath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType() ?: 'application/pdf',
            'uploaded_by' => $user?->id ?? 1,
            'notes' => 'Initial upload version 1.',
        ]);

        // Log Audit Trail
        $this->logActivity($document->id, 'uploaded', "Document '{$document->title}' uploaded (Version 1).");

        return $document;
    }

    /**
     * Upload a new version for an existing document.
     */
    public function addVersion(DigitalDocument $document, UploadedFile $file, ?string $notes = null): DocumentVersion
    {
        $user = Auth::user();
        $schoolId = $document->school_id;

        $folderPath = "private_documents/{$schoolId}/" . now()->format('Y/m');
        $storedPath = $file->store($folderPath, 'local');

        $newVersionNumber = $document->version + 1;

        $version = DocumentVersion::create([
            'document_id' => $document->id,
            'version_number' => $newVersionNumber,
            'file_path' => $storedPath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType() ?: 'application/pdf',
            'uploaded_by' => $user?->id ?? 1,
            'notes' => $notes ?? "Updated to version {$newVersionNumber}.",
        ]);

        // Update parent document pointer
        $document->update([
            'version' => $newVersionNumber,
            'file_path' => $storedPath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType() ?: 'application/pdf',
        ]);

        $this->logActivity($document->id, 'version_added', "Uploaded new version {$newVersionNumber}.");

        return $version;
    }

    /**
     * Update verification status (Verify, Reject, Mark Expired).
     */
    public function updateVerificationStatus(DigitalDocument $document, string $status, ?string $notes = null): void
    {
        $user = Auth::user();

        $document->update([
            'verification_status' => $status,
            'verified_by' => $user?->id,
            'verified_at' => now(),
            'verification_notes' => $notes,
        ]);

        $this->logActivity($document->id, 'verification_changed', "Verification status changed to '{$status}'. Notes: {$notes}");
    }

    /**
     * Save or update retention policy per document category.
     */
    public function updateRetentionPolicy(int $schoolId, string $category, int $months, bool $autoArchive = true, bool $autoDelete = false): DocumentRetentionPolicy
    {
        return DocumentRetentionPolicy::updateOrCreate(
            [
                'school_id' => $schoolId,
                'document_category' => $category,
            ],
            [
                'retention_period_months' => $months,
                'auto_archive' => $autoArchive,
                'auto_delete' => $autoDelete,
            ]
        );
    }

    /**
     * Calculate retention until date for a given category.
     */
    protected function calculateRetentionDate(int $schoolId, string $category): Carbon
    {
        $policy = DocumentRetentionPolicy::where('school_id', $schoolId)
            ->where('document_category', $category)
            ->first();

        $months = $policy ? $policy->retention_period_months : 36; // Default 3 years

        return Carbon::now()->addMonths($months);
    }

    /**
     * Log document activity audit trail.
     */
    public function logActivity(int $documentId, string $type, string $description): void
    {
        DocumentActivityLog::create([
            'document_id' => $documentId,
            'user_id' => Auth::id() ?? 1,
            'activity_type' => $type,
            'description' => $description,
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'user_agent' => request()->userAgent() ?? 'System Engine',
        ]);
    }
}
