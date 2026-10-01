<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AdmissionApplication;
use App\Models\AdmissionEnquiry;
use App\Models\AdmissionLead;
use App\Models\LeadCampaign;
use App\Models\LeadDocument;
use App\Models\LeadFollowUp;
use App\Models\LeadSource;
use App\Models\LeadStatusHistory;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdmissionSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('code', 'OIA-01')->first() ?? School::first();
        $academicYear = AcademicYear::where('school_id', $school->id)->first() ?? AcademicYear::first();
        $grade5 = SchoolClass::where('code', 'GR5')->first() ?? SchoolClass::first();
        $grade3 = SchoolClass::where('code', 'GR3')->first() ?? SchoolClass::skip(1)->first() ?? $grade5;

        // 1. Create Lead Sources
        $sourceWeb = LeadSource::updateOrCreate(
            ['code' => 'SRC-WEB'],
            ['school_id' => $school->id, 'name' => 'Website Inquiries', 'type' => 'digital', 'status' => 'active']
        );
        $sourceOpenHouse = LeadSource::updateOrCreate(
            ['code' => 'SRC-OPH'],
            ['school_id' => $school->id, 'name' => 'Open House Campus Visit', 'type' => 'event', 'status' => 'active']
        );
        $sourceSocial = LeadSource::updateOrCreate(
            ['code' => 'SRC-SOC'],
            ['school_id' => $school->id, 'name' => 'Social Media Campaign', 'type' => 'digital', 'status' => 'active']
        );
        $sourceReferral = LeadSource::updateOrCreate(
            ['code' => 'SRC-REF'],
            ['school_id' => $school->id, 'name' => 'Parent Referral', 'type' => 'referral', 'status' => 'active']
        );

        // 2. Create Lead Campaign
        $campaignFall = LeadCampaign::updateOrCreate(
            ['code' => 'CAMP-2026-FALL'],
            [
                'school_id' => $school->id,
                'name' => 'Fall 2026 Admissions Drive',
                'budget' => 50000.00,
                'start_date' => '2026-01-01',
                'end_date' => '2026-09-30',
                'status' => 'active',
            ]
        );

        // 3. Counselor User
        $counselorUser = User::updateOrCreate(
            ['email' => 'counselor@smartschool.edu'],
            [
                'school_id' => $school->id,
                'name' => 'Mark Davis (Lead Counselor)',
                'phone' => '+1 (555) 789-0123',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $counselorRole = Role::where('slug', 'counselor')->first() ?? Role::where('slug', 'admission-staff')->first();
        if ($counselorRole) {
            $counselorUser->assignRole($counselorRole);
        }

        // 4. Sample Leads Mapping Across All 11 Pipeline Stages
        $leadsData = [
            [
                'lead_code' => 'LEAD-2026-001',
                'parent_name' => 'Arthur Pendelton',
                'parent_email' => 'arthur.p@example.com',
                'parent_phone' => '+1 (555) 111-2222',
                'student_name' => 'Lucas Pendelton',
                'stage' => 'new_lead',
                'score' => 70,
                'source_id' => $sourceWeb->id,
                'class_id' => $grade5->id,
            ],
            [
                'lead_code' => 'LEAD-2026-002',
                'parent_name' => 'Clara Oswald',
                'parent_email' => 'clara.o@example.com',
                'parent_phone' => '+1 (555) 222-3333',
                'student_name' => 'Danny Oswald',
                'stage' => 'contacted',
                'score' => 75,
                'source_id' => $sourceSocial->id,
                'class_id' => $grade3->id,
            ],
            [
                'lead_code' => 'LEAD-2026-003',
                'parent_name' => 'David Miller',
                'parent_email' => 'david.m@example.com',
                'parent_phone' => '+1 (555) 333-4444',
                'student_name' => 'Sophie Miller',
                'stage' => 'counselling',
                'score' => 80,
                'source_id' => $sourceOpenHouse->id,
                'class_id' => $grade5->id,
            ],
            [
                'lead_code' => 'LEAD-2026-004',
                'parent_name' => 'Jennifer Adams',
                'parent_email' => 'jennifer.a@example.com',
                'parent_phone' => '+1 (555) 444-5555',
                'student_name' => 'Michael Adams',
                'stage' => 'campus_visit',
                'score' => 85,
                'source_id' => $sourceOpenHouse->id,
                'class_id' => $grade5->id,
            ],
            [
                'lead_code' => 'LEAD-2026-005',
                'parent_name' => 'Robert Taylor',
                'parent_email' => 'robert.t@example.com',
                'parent_phone' => '+1 (555) 555-6666',
                'student_name' => 'Chloe Taylor',
                'stage' => 'application_started',
                'score' => 85,
                'source_id' => $sourceReferral->id,
                'class_id' => $grade3->id,
            ],
            [
                'lead_code' => 'LEAD-2026-006',
                'parent_name' => 'Susan Clark',
                'parent_email' => 'susan.c@example.com',
                'parent_phone' => '+1 (555) 666-7777',
                'student_name' => 'Noah Clark',
                'stage' => 'documents_pending',
                'score' => 90,
                'source_id' => $sourceWeb->id,
                'class_id' => $grade5->id,
            ],
            [
                'lead_code' => 'LEAD-2026-007',
                'parent_name' => 'Thomas White',
                'parent_email' => 'thomas.w@example.com',
                'parent_phone' => '+1 (555) 777-8888',
                'student_name' => 'James White',
                'stage' => 'application_submitted',
                'score' => 95,
                'source_id' => $sourceWeb->id,
                'class_id' => $grade5->id,
                'app_code' => 'APP-2026-001',
            ],
            [
                'lead_code' => 'LEAD-2026-008',
                'parent_name' => 'Patricia Harris',
                'parent_email' => 'patricia.h@example.com',
                'parent_phone' => '+1 (555) 888-9999',
                'student_name' => 'Olivia Harris',
                'stage' => 'under_review',
                'score' => 95,
                'source_id' => $sourceOpenHouse->id,
                'class_id' => $grade3->id,
                'app_code' => 'APP-2026-002',
            ],
            [
                'lead_code' => 'LEAD-2026-009',
                'parent_name' => 'Charles Martin',
                'parent_email' => 'charles.m@example.com',
                'parent_phone' => '+1 (555) 999-0000',
                'student_name' => 'Ethan Martin',
                'stage' => 'approved',
                'score' => 98,
                'source_id' => $sourceReferral->id,
                'class_id' => $grade5->id,
                'app_code' => 'APP-2026-003',
            ],
            [
                'lead_code' => 'LEAD-2026-010',
                'parent_name' => 'Margaret Thompson',
                'parent_email' => 'margaret.t@example.com',
                'parent_phone' => '+1 (555) 000-1111',
                'student_name' => 'Emily Thompson',
                'stage' => 'admission_confirmed',
                'score' => 100,
                'source_id' => $sourceReferral->id,
                'class_id' => $grade5->id,
                'app_code' => 'APP-2026-004',
            ],
            [
                'lead_code' => 'LEAD-2026-011',
                'parent_name' => 'Richard Garcia',
                'parent_email' => 'richard.g@example.com',
                'parent_phone' => '+1 (555) 432-1098',
                'student_name' => 'Daniel Garcia',
                'stage' => 'lost',
                'score' => 20,
                'source_id' => $sourceSocial->id,
                'class_id' => $grade3->id,
                'loss_reason' => 'Family relocated to another state.',
            ],
        ];

        foreach ($leadsData as $ld) {
            $lead = AdmissionLead::updateOrCreate(
                ['lead_code' => $ld['lead_code']],
                [
                    'school_id' => $school->id,
                    'source_id' => $ld['source_id'],
                    'campaign_id' => $campaignFall->id,
                    'assigned_counselor_id' => $counselorUser->id,
                    'grade_applying_for_id' => $ld['class_id'],
                    'academic_year_id' => $academicYear ? $academicYear->id : null,
                    'parent_name' => $ld['parent_name'],
                    'parent_email' => $ld['parent_email'],
                    'parent_phone' => $ld['parent_phone'],
                    'student_name' => $ld['student_name'],
                    'student_dob' => '2015-06-15',
                    'gender' => 'male',
                    'stage' => $ld['stage'],
                    'status' => $ld['stage'] === 'admission_confirmed' ? 'converted' : ($ld['stage'] === 'lost' ? 'lost' : 'active'),
                    'score' => $ld['score'],
                    'loss_reason' => $ld['loss_reason'] ?? null,
                ]
            );

            // Log Initial Stage History
            LeadStatusHistory::updateOrCreate(
                ['lead_id' => $lead->id, 'to_stage' => $ld['stage']],
                [
                    'changed_by' => $counselorUser->id,
                    'from_stage' => 'new_lead',
                    'reason' => 'Initial lead entry & stage assignment.',
                ]
            );

            // Create Application if app_code specified
            if (isset($ld['app_code'])) {
                AdmissionApplication::updateOrCreate(
                    ['application_number' => $ld['app_code']],
                    [
                        'lead_id' => $lead->id,
                        'school_id' => $school->id,
                        'class_id' => $ld['class_id'],
                        'academic_year_id' => $academicYear ? $academicYear->id : null,
                        'application_stage' => $ld['stage'] === 'admission_confirmed' ? 'enrolled' : 'submitted',
                        'submission_date' => now()->subDays(5),
                        'fee_status' => $ld['stage'] === 'admission_confirmed' ? 'paid' : 'pending',
                    ]
                );
            }
        }

        // 5. Follow-ups (Include 1 TODAY and 1 OVERDUE)
        $leadNew = AdmissionLead::where('lead_code', 'LEAD-2026-001')->first();
        if ($leadNew) {
            LeadFollowUp::updateOrCreate(
                ['lead_id' => $leadNew->id, 'subject' => 'Initial Phone Counseling Call'],
                [
                    'assigned_to' => $counselorUser->id,
                    'type' => 'phone_call',
                    'notes' => 'Discuss STEM curriculum & school fee installment plans.',
                    'scheduled_at' => now()->setTime(14, 0), // Today at 2:00 PM
                    'due_date' => now()->format('Y-m-d'),
                    'status' => 'pending',
                ]
            );

            AdmissionEnquiry::updateOrCreate(
                ['lead_id' => $leadNew->id, 'question' => 'What are the transportation routes for Grade 5?'],
                [
                    'answered_by' => $counselorUser->id,
                    'inquiry_type' => 'transport',
                    'response' => 'Provided route map 4 covering North Springfield neighborhood.',
                    'inquiry_date' => now()->subHours(2),
                ]
            );
        }

        $leadContacted = AdmissionLead::where('lead_code', 'LEAD-2026-002')->first();
        if ($leadContacted) {
            LeadFollowUp::updateOrCreate(
                ['lead_id' => $leadContacted->id, 'subject' => 'Schedule Campus Tour & Meeting'],
                [
                    'assigned_to' => $counselorUser->id,
                    'type' => 'meeting',
                    'notes' => 'Follow up on open house invitation.',
                    'scheduled_at' => now()->subDays(2)->setTime(10, 0), // 2 Days ago (OVERDUE)
                    'due_date' => now()->subDays(2)->format('Y-m-d'),
                    'status' => 'pending',
                ]
            );
        }

        // Document for Lead 7 (James White)
        $leadSubmitted = AdmissionLead::where('lead_code', 'LEAD-2026-007')->first();
        if ($leadSubmitted) {
            LeadDocument::updateOrCreate(
                ['lead_id' => $leadSubmitted->id, 'title' => 'Grade 4 Official Report Card'],
                [
                    'document_type' => 'report_card',
                    'file_path' => 'lead_documents/james_white_report_card.pdf',
                    'file_size' => 150000,
                    'mime_type' => 'application/pdf',
                    'uploaded_by' => $counselorUser->id,
                ]
            );
        }
    }
}
