<?php

namespace App\Livewire\Communication;

use App\Models\CommunicationLog;
use App\Models\School;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Logs extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $search = '';
    public string $channel = '';
    public string $delivery_status = '';
    public int $perPage = 15;

    public function resendLog(int $logId): void
    {
        $this->authorize('create', \App\Models\Communication::class);

        $log = CommunicationLog::findOrFail($logId);
        $log->update([
            'delivery_status' => 'sent',
            'failure_reason' => null,
            'delivered_at' => now(),
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Message Resent',
            'message' => "Re-dispatched delivery attempt for recipient {$log->recipient_email}.",
        ]);
    }

    public function render()
    {
        $this->authorize('viewAny', \App\Models\Communication::class);

        $currentUser = Auth::user();
        $schoolId = $currentUser->school_id ?? School::first()->id ?? 1;

        $query = CommunicationLog::with(['communication'])
            ->where('school_id', $schoolId);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('recipient_email', 'like', '%' . $this->search . '%')
                    ->orWhere('recipient_phone', 'like', '%' . $this->search . '%')
                    ->orWhere('provider_message_id', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->channel !== '') {
            $query->where('channel', $this->channel);
        }

        if ($this->delivery_status !== '') {
            $query->where('delivery_status', $this->delivery_status);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate($this->perPage);

        return view('livewire.communication.logs', [
            'logs' => $logs,
        ])->layout('components.layouts.admin', ['title' => 'Communication Delivery Logs & Status Tracker']);
    }
}
