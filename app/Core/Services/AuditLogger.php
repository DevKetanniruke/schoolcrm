<?php

namespace App\Core\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    /**
     * Log a user/system action into the audit database and log channel.
     */
    public function log(string $action, ?string $entityType = null, ?int $entityId = null, array $payload = [], ?int $schoolId = null): void
    {
        $userId = auth()->id();
        $schoolId = $schoolId ?? (auth()->check() ? auth()->user()->school_id : null);
        $ip = request()->ip();
        $userAgent = request()->userAgent();

        try {
            AuditLog::create([
                'school_id' => $schoolId,
                'user_id' => $userId,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'payload' => $payload,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);

            Log::channel('audit')->info("Audit Log [{$action}]", [
                'user_id' => $userId,
                'school_id' => $schoolId,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'payload' => $payload,
                'ip' => $ip,
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to write audit log: {$e->getMessage()}", [
                'action' => $action,
                'exception' => $e,
            ]);
        }
    }
}
