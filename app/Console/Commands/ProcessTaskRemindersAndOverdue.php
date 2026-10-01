<?php

namespace App\Console\Commands;

use App\Core\Tasks\Services\TaskService;
use Illuminate\Console\Command;

class ProcessTaskRemindersAndOverdue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:process-reminders-and-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process CRM task reminders and flag past-due tasks as overdue';

    /**
     * Execute the console command.
     */
    public function handle(TaskService $taskService): int
    {
        $this->info('Starting CRM task engine background sync...');

        $overdueCount = $taskService->checkAndFlagOverdueTasks();
        $this->info("Flagged {$overdueCount} task(s) as overdue.");

        $remindersCount = $taskService->processDueReminders();
        $this->info("Dispatched {$remindersCount} task reminder(s).");

        $this->info('CRM task engine background sync completed successfully.');

        return Command::SUCCESS;
    }
}
