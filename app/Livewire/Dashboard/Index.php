<?php

namespace App\Livewire\Dashboard;

use App\Models\AuditLog;
use App\Models\School;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Index extends Component
{
    public string $timeframe = 'this_month';
    public bool $loading = false;

    public function triggerToast(string $type = 'success')
    {
        $messages = [
            'success' => 'System configuration & cache synchronized successfully!',
            'info' => 'System audit report has been queued for export.',
            'warning' => '3 fee invoices are nearing payment deadline.',
            'error' => 'Database connection retry limit reached for backup slave.',
        ];

        $this->dispatch('notify', [
            'type' => $type,
            'title' => ucfirst($type) . ' Notification',
            'message' => $messages[$type] ?? 'Operation completed.',
        ]);
    }

    #[Layout('components.layouts.admin')]
    public function render()
    {
        $stats = [
            'total_students' => 1248,
            'active_staff' => 96,
            'monthly_revenue' => '$142,850',
            'attendance_rate' => '94.8%',
        ];

        $activities = AuditLog::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.dashboard.index', [
            'stats' => $stats,
            'activities' => $activities,
            'school' => School::first(),
        ]);
    }
}
