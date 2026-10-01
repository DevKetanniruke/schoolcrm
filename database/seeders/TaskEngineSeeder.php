<?php

namespace Database\Seeders;

use App\Core\Tasks\Services\TaskService;
use App\Models\AdmissionApplication;
use App\Models\AdmissionLead;
use App\Models\CrmTask;
use App\Models\ParentComplaint;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\StaffProfile;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskEngineSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::first();
        if (!$school) {
            return;
        }

        $admin = User::where('email', 'admin@smartschool.com')->first() ?? User::first();
        $teacherUser = User::where('email', 'sarah.jenkins@smartschool.com')->first() ?? $admin;
        $counselorUser = User::where('email', 'robert.admission@smartschool.com')->first() ?? $admin;

        /** @var TaskService $taskService */
        $taskService = app(TaskService::class);

        // Fetch sample entities for dynamic polymorphic linkages
        $student = Student::first();
        $parent = ParentProfile::first();
        $lead = AdmissionLead::first();
        $staff = StaffProfile::first();
        $application = AdmissionApplication::first();
        $complaint = ParentComplaint::first();

        // 1. Overdue Admission Lead Follow-up
        if ($lead) {
            $t1 = $taskService->createTask([
                'school_id' => $school->id,
                'title' => 'Follow up on admission enquiry documentation for ' . $lead->student_first_name,
                'description' => 'Contact parent to request previous school transcripts and birth certificate verification.',
                'taskable_type' => AdmissionLead::class,
                'taskable_id' => $lead->id,
                'assigned_to' => $counselorUser->id,
                'priority' => 'urgent',
                'status' => 'pending',
                'due_at' => now()->subDays(2), // Overdue!
                'reminder_at' => now()->subDays(2)->subHours(2),
                'reminder_channel' => 'in_app',
            ], $admin);

            $taskService->addComment($t1, 'Attempted call on Monday, parent requested evening call back.', null, null, $counselorUser);
        }

        // 2. Today's Student Counseling Task
        if ($student) {
            $t2 = $taskService->createTask([
                'school_id' => $school->id,
                'title' => 'Conduct Mid-Term Performance Review for ' . $student->first_name . ' ' . $student->last_name,
                'description' => 'Review mathematics performance drop and assign peer tutor.',
                'taskable_type' => Student::class,
                'taskable_id' => $student->id,
                'assigned_to' => $teacherUser->id,
                'priority' => 'high',
                'status' => 'in_progress',
                'due_at' => now()->endOfDay(), // Due today!
                'reminder_at' => now()->addHours(1),
                'reminder_channel' => 'email',
            ], $admin);

            $taskService->addComment($t2, 'Scheduled meeting with student during 5th period.', null, null, $teacherUser);
        }

        // 3. Parent Complaint Investigation
        if ($complaint) {
            $t3 = $taskService->createTask([
                'school_id' => $school->id,
                'title' => 'Investigate complaint ticket #' . $complaint->ticket_number,
                'description' => 'Coordinate with transport department regarding bus route delay.',
                'taskable_type' => ParentComplaint::class,
                'taskable_id' => $complaint->id,
                'assigned_to' => $admin->id,
                'priority' => 'high',
                'status' => 'pending',
                'due_at' => now()->addDays(1),
            ], $admin);

            $taskService->addComment($t3, 'Contacted transport manager for bus GPS logs.', null, null, $admin);
        }

        // 4. Staff Performance Review Task
        if ($staff) {
            $t4 = $taskService->createTask([
                'school_id' => $school->id,
                'title' => 'Annual Performance Appraisal for ' . $staff->first_name . ' ' . $staff->last_name,
                'description' => 'Complete classroom observation report and submit evaluation summary.',
                'taskable_type' => StaffProfile::class,
                'taskable_id' => $staff->id,
                'assigned_to' => $admin->id,
                'priority' => 'medium',
                'status' => 'completed',
                'due_at' => now()->subDays(5),
            ], $admin);

            $taskService->updateStatus($t4, 'completed', $admin);
        }

        // 5. Standalone School Setup Task
        $t5 = $taskService->createTask([
            'school_id' => $school->id,
            'title' => 'Prepare Academic Calendar and Holiday List for Upcoming Quarter',
            'description' => 'Draft quarterly exam timetables and holiday schedule for principal approval.',
            'taskable_type' => School::class,
            'taskable_id' => $school->id,
            'assigned_to' => $admin->id,
            'priority' => 'medium',
            'status' => 'pending',
            'due_at' => now()->addDays(3),
            'is_recurring' => true,
            'recurrence_pattern' => 'monthly',
        ], $admin);

        // Run overdue check to update t1 status to 'overdue'
        $taskService->checkAndFlagOverdueTasks($school->id);
    }
}
