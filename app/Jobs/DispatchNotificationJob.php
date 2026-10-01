<?php

namespace App\Jobs;

use App\Core\Services\CentralizedNotificationEngine;
use App\Models\Communication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class DispatchNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public Communication $communication;
    public array $recipient;
    public string $channel;

    /**
     * Create a new job instance.
     */
    public function __construct(Communication $communication, array $recipient, string $channel = 'email')
    {
        $this->communication = $communication;
        $this->recipient = $recipient;
        $this->channel = $channel;
    }

    /**
     * Execute the job.
     */
    public function handle(CentralizedNotificationEngine $notificationEngine): void
    {
        $notificationEngine->processSingleChannel(
            $this->communication,
            $this->recipient,
            $this->channel
        );
    }

    /**
     * Handle job failure after retries exhausted.
     */
    public function failed(Throwable $exception): void
    {
        $notificationEngine = app(CentralizedNotificationEngine::class);
        $notificationEngine->markJobFailed(
            $this->communication,
            $this->recipient,
            $this->channel,
            $exception->getMessage()
        );
    }
}
