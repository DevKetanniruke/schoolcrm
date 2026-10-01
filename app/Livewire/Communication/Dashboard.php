<?php

namespace App\Livewire\Communication;

use App\Models\Communication;
use App\Models\CommunicationLog;
use App\Models\CommunicationTemplate;
use App\Models\School;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    use AuthorizesRequests;

    public function render()
    {
        $this->authorize('viewAny', Communication::class);

        $currentUser = Auth::user();
        $schoolId = $currentUser->school_id ?? School::first()->id ?? 1;

        $commsQuery = Communication::where('school_id', $schoolId);

        $totalDispatches = (clone $commsQuery)->count();
        $totalSent = (clone $commsQuery)->whereIn('status', ['sent', 'partially_failed'])->count();
        $queuedScheduled = (clone $commsQuery)->where('status', 'queued')->whereNotNull('scheduled_at')->count();

        $logsQuery = CommunicationLog::where('school_id', $schoolId);
        $totalLogsCount = (clone $logsQuery)->count();
        $deliveredCount = (clone $logsQuery)->whereIn('delivery_status', ['delivered', 'read'])->count();
        $failedCount = (clone $logsQuery)->where('delivery_status', 'failed')->count();

        $successRate = $totalLogsCount > 0 ? round(($deliveredCount / $totalLogsCount) * 100, 1) : 100.0;

        // Channel Breakdown
        $channelStats = [
            'email' => (clone $logsQuery)->where('channel', 'email')->count(),
            'sms' => (clone $logsQuery)->where('channel', 'sms')->count(),
            'whatsapp' => (clone $logsQuery)->where('channel', 'whatsapp')->count(),
            'in_app' => (clone $logsQuery)->where('channel', 'in_app')->count(),
        ];

        // Recent Communications Feed
        $recentDispatches = (clone $commsQuery)
            ->with(['sender', 'template'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Active Templates Count
        $activeTemplatesCount = CommunicationTemplate::where('school_id', $schoolId)->where('status', 'active')->count();

        return view('livewire.communication.dashboard', [
            'totalDispatches' => $totalDispatches,
            'totalSent' => $totalSent,
            'queuedScheduled' => $queuedScheduled,
            'deliveredCount' => $deliveredCount,
            'failedCount' => $failedCount,
            'successRate' => $successRate,
            'channelStats' => $channelStats,
            'recentDispatches' => $recentDispatches,
            'activeTemplatesCount' => $activeTemplatesCount,
        ])->layout('components.layouts.admin', ['title' => 'School Communication CRM Executive Dashboard']);
    }
}
