<?php

namespace App\Http\Controllers;

use App\Models\DigitalDocument;
use App\Models\DocumentActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentStreamController extends Controller
{
    /**
     * Preview private document securely inline.
     */
    public function preview(Request $request, DigitalDocument $document): BinaryFileResponse
    {
        $this->authorizeDocumentAccess($document);

        $this->logActivity($document, 'previewed', 'User previewed private document inline.');

        $fullPath = Storage::disk('local')->path($document->file_path);

        if (! file_exists($fullPath)) {
            abort(404, 'Private document file not found on disk.');
        }

        return response()->file($fullPath, [
            'Content-Type' => $document->mime_type,
            'Content-Disposition' => 'inline; filename="' . $document->file_name . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, private',
        ]);
    }

    /**
     * Download private document securely.
     */
    public function download(Request $request, DigitalDocument $document): BinaryFileResponse
    {
        $this->authorizeDocumentAccess($document);

        $this->logActivity($document, 'downloaded', 'User downloaded private document file.');

        $fullPath = Storage::disk('local')->path($document->file_path);

        if (! file_exists($fullPath)) {
            abort(404, 'Private document file not found on disk.');
        }

        return response()->download($fullPath, $document->file_name, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, private',
        ]);
    }

    /**
     * Authorize user access based on tenant isolation and confidentiality rules.
     */
    protected function authorizeDocumentAccess(DigitalDocument $document): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        // Multi-tenant check
        if (! $user->hasRole('super-admin') && $user->school_id && $document->school_id !== $user->school_id) {
            abort(403, 'Unauthorized access to school private document.');
        }

        // Confidential document access check
        if ($document->is_confidential && ! $user->hasRole('super-admin')) {
            if (! $user->hasPermissionTo('documents.view_confidential') && $document->uploaded_by !== $user->id) {
                abort(403, 'Access denied to confidential document.');
            }
        }
    }

    /**
     * Record document access audit trail entry.
     */
    protected function logActivity(DigitalDocument $document, string $type, string $description): void
    {
        DocumentActivityLog::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'activity_type' => $type,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
