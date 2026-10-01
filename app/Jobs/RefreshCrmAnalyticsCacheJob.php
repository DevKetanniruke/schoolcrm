<?php

namespace App\Jobs;

use App\Core\Services\CrmAnalyticsService;
use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshCrmAnalyticsCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?int $schoolId;

    /**
     * Create a new job instance.
     */
    public function __construct(?int $schoolId = null)
    {
        $this->schoolId = $schoolId;
    }

    /**
     * Execute the job.
     */
    public function handle(CrmAnalyticsService $analyticsService): void
    {
        $schools = $this->schoolId ? School::where('id', $this->schoolId)->get() : School::all();

        foreach ($schools as $school) {
            $filters = ['school_id' => $school->id, 'date_preset' => 'this_month'];

            $analyticsService->getExecutiveMetrics($filters, true);
            $analyticsService->getAdmissionsMetrics($filters, true);
            $analyticsService->getCommunicationMetrics($filters, true);
            $analyticsService->getStudentEngagementMetrics($filters, true);
            $analyticsService->getParentEngagementMetrics($filters, true);
            $analyticsService->getStaffTasksMetrics($filters, true);
        }
    }
}
