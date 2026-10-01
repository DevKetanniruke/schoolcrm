<?php

namespace App\Jobs;

use App\Models\Communication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchBulkNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Communication $communication;
    public array $recipients;
    public array $channels;

    /**
     * Create a new job instance.
     */
    public function __construct(Communication $communication, array $recipients, array $channels = ['email'])
    {
        $this->communication = $communication;
        $this->recipients = $recipients;
        $this->channels = $channels;
    }

    /**
     * Execute the job: Dispatch individual async child jobs for each recipient & channel.
     */
    public function handle(): void
    {
        $this->communication->update(['status' => 'processing']);

        foreach ($this->recipients as $recipient) {
            foreach ($this->channels as $channel) {
                DispatchNotificationJob::dispatch(
                    $this->communication,
                    $recipient,
                    $channel
                );
            }
        }
    }
}
