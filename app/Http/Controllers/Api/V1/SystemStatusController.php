<?php

namespace App\Http\Controllers\Api\V1;

use App\Core\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SystemStatusController extends Controller
{
    use ApiResponse;

    /**
     * Return CRM application status and version info.
     */
    public function status(): JsonResponse
    {
        return $this->success([
            'system' => config('app.name', 'SmartSchool CRM'),
            'version' => '1.0.0',
            'environment' => config('app.env'),
            'status' => 'operational',
            'timestamp' => now()->toIso8601String(),
        ], 'System status check successful');
    }
}
