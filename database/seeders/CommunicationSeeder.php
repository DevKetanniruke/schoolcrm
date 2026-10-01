<?php

namespace Database\Seeders;

use App\Core\Communication\Services\CommunicationManager;
use App\Models\Communication;
use App\Models\CommunicationLog;
use App\Models\CommunicationTemplate;
use App\Models\NotificationPreference;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommunicationSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('code', 'OIA-01')->first() ?? School::first();
        $admin = User::where('school_id', $school->id)->first() ?? User::first();

        // 1. Create Standard Educational Message Templates
        $tplFee = CommunicationTemplate::updateOrCreate(
            ['code' => 'TPL-FEE-DUE'],
            [
                'school_id' => $school->id,
                'title' => 'Tuition Fee Payment Due Reminder',
                'channel' => 'multi_channel',
                'category' => 'fees',
                'subject' => 'Notice: Upcoming Tuition Fee Deadline for {school_name}',
                'content_body' => "Dear {parent_name},\n\nThis is a friendly reminder that the tuition fee payment for {student_name} is due next week. Please log in to your parent portal or visit the school accounts desk to complete payment.\n\nThank you,\n{school_name} Finance Office",
                'variables_json' => ['parent_name', 'student_name', 'school_name'],
                'status' => 'active',
                'created_by' => $admin?->id,
            ]
        );

        $tplAttendance = CommunicationTemplate::updateOrCreate(
            ['code' => 'TPL-ATT-ABSENT'],
            [
                'school_id' => $school->id,
                'title' => 'Student Unexcused Absence Notification',
                'channel' => 'whatsapp',
                'category' => 'attendance',
                'subject' => 'Absence Notice: {student_name}',
                'content_body' => "Dear {parent_name}, your child {student_name} was marked absent for today's morning assembly at {school_name}. If this is an authorized absence, please notify the class teacher via the portal.",
                'variables_json' => ['parent_name', 'student_name', 'school_name'],
                'status' => 'active',
                'created_by' => $admin?->id,
            ]
        );

        $tplEvent = CommunicationTemplate::updateOrCreate(
            ['code' => 'TPL-EVT-OPENHOUSE'],
            [
                'school_id' => $school->id,
                'title' => 'Annual Open House & Science Fair Invitation',
                'channel' => 'email',
                'category' => 'events',
                'subject' => 'You are invited: {school_name} Open House & Exhibition',
                'content_body' => "Dear {parent_name},\n\nWe cordially invite you and your family to attend the {school_name} Annual Science Exhibition and Campus Open House this Saturday from 9:00 AM.\n\nWarm regards,\nSchool Administration",
                'variables_json' => ['parent_name', 'school_name'],
                'status' => 'active',
                'created_by' => $admin?->id,
            ]
        );

        $tplEmergency = CommunicationTemplate::updateOrCreate(
            ['code' => 'TPL-EMG-WEATHER'],
            [
                'school_id' => $school->id,
                'title' => 'Emergency Severe Weather Closure Notice',
                'channel' => 'sms',
                'category' => 'emergency',
                'subject' => 'EMERGENCY: {school_name} Closure',
                'content_body' => "EMERGENCY NOTICE: Due to severe weather warnings, {school_name} will remain closed tomorrow. Online classes will resume via portal. Stay safe.",
                'variables_json' => ['school_name'],
                'status' => 'active',
                'created_by' => $admin?->id,
            ]
        );

        // 2. Create Sample Historical Communications
        $comm1 = Communication::create([
            'school_id' => $school->id,
            'sender_id' => $admin?->id,
            'template_id' => $tplFee->id,
            'title' => 'Grade 5 Tuition Fee Reminder Broadcast',
            'channel' => 'email',
            'recipient_type' => 'parent_group',
            'subject' => 'Notice: Upcoming Tuition Fee Deadline for ' . $school->name,
            'message_body' => $tplFee->content_body,
            'scheduled_at' => null,
            'status' => 'sent',
            'total_recipients' => 5,
            'success_count' => 5,
            'failure_count' => 0,
            'sent_at' => now()->subHours(5),
        ]);

        // Create log entries for comm1
        for ($i = 1; $i <= 5; $i++) {
            CommunicationLog::create([
                'communication_id' => $comm1->id,
                'school_id' => $school->id,
                'recipient_type' => 'parent',
                'recipient_id' => $i,
                'recipient_email' => "parent{$i}@example.com",
                'recipient_phone' => "+1 555-000-000{$i}",
                'channel' => 'email',
                'provider_name' => 'log_email',
                'provider_message_id' => 'MSG-LOG-' . uniqid(),
                'delivery_status' => $i === 5 ? 'delivered' : 'read',
                'delivered_at' => now()->subHours(5),
                'read_at' => $i < 5 ? now()->subHours(3) : null,
            ]);
        }

        $comm2 = Communication::create([
            'school_id' => $school->id,
            'sender_id' => $admin?->id,
            'template_id' => $tplEmergency->id,
            'title' => 'School-wide Severe Weather Emergency SMS Broadcast',
            'channel' => 'sms',
            'recipient_type' => 'school_wide',
            'subject' => 'EMERGENCY NOTICE: School Closure',
            'message_body' => $tplEmergency->content_body,
            'scheduled_at' => null,
            'status' => 'sent',
            'total_recipients' => 3,
            'success_count' => 3,
            'failure_count' => 0,
            'sent_at' => now()->subDays(1),
        ]);

        for ($i = 1; $i <= 3; $i++) {
            CommunicationLog::create([
                'communication_id' => $comm2->id,
                'school_id' => $school->id,
                'recipient_type' => 'user',
                'recipient_id' => $i,
                'recipient_phone' => "+1 555-111-222{$i}",
                'channel' => 'sms',
                'provider_name' => 'log_sms',
                'provider_message_id' => 'SMS-LOG-' . uniqid(),
                'delivery_status' => 'delivered',
                'delivered_at' => now()->subDays(1),
            ]);
        }

        // 3. Create Sample Notification Preferences
        NotificationPreference::updateOrCreate(
            ['school_id' => $school->id, 'user_id' => $admin?->id, 'entity_type' => 'user', 'entity_id' => $admin?->id],
            [
                'channel_in_app' => true,
                'channel_email' => true,
                'channel_sms' => true,
                'channel_whatsapp' => true,
                'category_academic' => true,
                'category_attendance' => true,
                'category_fees' => true,
                'category_events' => true,
                'category_emergency' => true,
            ]
        );
    }
}
