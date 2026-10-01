<?php

use App\Http\Controllers\DocumentStreamController;
use App\Livewire\Admin\AutomationRules;
use App\Livewire\Admin\CrmAnalyticsDashboard;
use App\Livewire\Admin\DocumentVault;
use App\Livewire\Admin\NotificationCenter;
use App\Livewire\Admin\Hierarchy\Index as HierarchyIndex;
use App\Livewire\Admin\ManagementIntelligence;
use App\Livewire\Admin\Permissions\Index as PermissionsIndex;
use App\Livewire\Admin\Roles\Index as RolesIndex;
use App\Livewire\Admin\Users\Index as UsersIndex;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Profile\EditProfile;
use Illuminate\Support\Facades\Route;

use App\Livewire\Students\EngagementAnalytics;
use App\Livewire\Students\Index as StudentIndex;
use App\Livewire\Students\Show360 as StudentShow360;

use App\Livewire\Parents\Index as ParentIndex;
use App\Livewire\Parents\Show360 as ParentShow360;
use App\Livewire\Parents\FamilyDashboard as FamilyDashboard;
use App\Livewire\Parents\CommunicationLogIndex as CommunicationLogIndex;

use App\Livewire\Staff\Index as StaffIndex;
use App\Livewire\Staff\Show360 as StaffShow360;
use App\Livewire\Staff\AssignmentMatrix as StaffAssignmentMatrix;

use App\Livewire\Admissions\Dashboard as AdmissionsDashboard;
use App\Livewire\Admissions\Kanban as AdmissionsKanban;
use App\Livewire\Admissions\Index as AdmissionsIndex;
use App\Livewire\Admissions\Show360 as AdmissionsShow360;

use App\Livewire\Communication\AiParentCommunication;
use App\Livewire\Communication\Composer as CommunicationComposer;
use App\Livewire\Communication\Dashboard as CommunicationDashboard;
use App\Livewire\Communication\Logs as CommunicationLogs;
use App\Livewire\Communication\Templates as CommunicationTemplates;

Route::get('/', DashboardIndex::class)->name('home');

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'mr'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard');
    Route::get('/profile', EditProfile::class)->name('profile.edit');

    // Student 360° Module
    Route::get('/students', StudentIndex::class)->middleware('permission:students.view')->name('students.index');
    Route::get('/students/engagement-analytics', EngagementAnalytics::class)->middleware('permission:students.view')->name('students.engagement-analytics');
    Route::get('/students/{student}', StudentShow360::class)->middleware('permission:students.view')->name('students.show');

    // Parent & Family Relationship Management System
    Route::get('/parents', ParentIndex::class)->middleware('permission:parents.view')->name('parents.index');
    Route::get('/parents/family-dashboard', FamilyDashboard::class)->name('parents.family-dashboard');
    Route::get('/parents/communication-log', CommunicationLogIndex::class)->middleware('permission:parents.view')->name('parents.communication-log');
    Route::get('/parents/{parent}', ParentShow360::class)->middleware('permission:parents.view')->name('parents.show');

    // Teacher & Staff Management System
    Route::get('/staff', StaffIndex::class)->middleware('permission:staff.view')->name('staff.index');
    Route::get('/staff/matrix', StaffAssignmentMatrix::class)->middleware('permission:staff.view')->name('staff.matrix');
    Route::get('/staff/{staff}', StaffShow360::class)->middleware('permission:staff.view')->name('staff.show');

    // Admission CRM System
    Route::get('/admissions/dashboard', AdmissionsDashboard::class)->middleware('permission:admissions.view')->name('admissions.dashboard');
    Route::get('/admissions/pipeline', AdmissionsKanban::class)->middleware('permission:admissions.view')->name('admissions.pipeline');
    Route::get('/admissions/leads', AdmissionsIndex::class)->middleware('permission:admissions.view')->name('admissions.index');
    Route::get('/admissions/leads/{lead}', AdmissionsShow360::class)->middleware('permission:admissions.view')->name('admissions.show');
    Route::get('/admissions/leads/{lead}/view', AdmissionsShow360::class)->middleware('permission:admissions.view')->name('admissions.leads.show');

    // Communication CRM System
    Route::get('/communication/dashboard', CommunicationDashboard::class)->middleware('permission:communications.view')->name('communication.dashboard');
    Route::get('/communication/ai-assistant', AiParentCommunication::class)->middleware('permission:communications.send')->name('communication.ai-assistant');
    Route::get('/communication/composer', CommunicationComposer::class)->middleware('permission:communications.send')->name('communication.composer');
    Route::get('/communication/templates', CommunicationTemplates::class)->middleware('permission:communications.templates')->name('communication.templates');
    Route::get('/communication/logs', CommunicationLogs::class)->middleware('permission:communications.view')->name('communication.logs');

    // CRM Task & Follow-up Engine
    Route::get('/tasks/dashboard', \App\Livewire\Tasks\Dashboard::class)->middleware('permission:tasks.view')->name('tasks.dashboard');
    Route::get('/tasks/{id}', \App\Livewire\Tasks\Workspace::class)->middleware('permission:tasks.view')->name('tasks.show');

    // AI Intelligence Layer
    Route::get('/ai/assistant', \App\Livewire\Ai\AssistantWorkspace::class)->name('ai.assistant');

    // Digital Document Management System
    Route::get('/documents/vault', DocumentVault::class)->middleware('permission:users.view')->name('documents.vault');
    Route::get('/documents/{document}/preview', [DocumentStreamController::class, 'preview'])->name('documents.preview');
    Route::get('/documents/{document}/download', [DocumentStreamController::class, 'download'])->name('documents.download');

    // AI Weekly Assessment System
    Route::get('/assessments/dashboard', \App\Livewire\Assessments\AssessmentDashboard::class)->name('assessments.dashboard');
    Route::get('/assessments/questions', \App\Livewire\Assessments\QuestionBank::class)->name('assessments.questions');
    Route::get('/assessments/manage', \App\Livewire\Assessments\AssessmentManager::class)->name('assessments.manage');
    Route::get('/assessments/take/{attemptId}', \App\Livewire\Assessments\StudentTestPlayer::class)->name('assessments.take');

    // Administration Panels
    Route::get('/admin/notifications', NotificationCenter::class)->middleware('permission:users.view')->name('admin.notifications');
    Route::get('/admin/management-intelligence', ManagementIntelligence::class)->middleware('permission:users.view')->name('admin.management-intelligence');
    Route::get('/admin/automation-rules', AutomationRules::class)->middleware('permission:users.view')->name('admin.automation-rules');
    Route::get('/admin/crm-analytics', CrmAnalyticsDashboard::class)->middleware('permission:users.view')->name('admin.crm-analytics');
    Route::get('/admin/hierarchy', HierarchyIndex::class)->name('admin.hierarchy.index');
    Route::get('/admin/users', UsersIndex::class)->middleware('permission:users.view')->name('admin.users.index');
    Route::get('/admin/roles', RolesIndex::class)->middleware('permission:roles.view')->name('admin.roles.index');
    Route::get('/admin/permissions', PermissionsIndex::class)->middleware('permission:permissions.view')->name('admin.permissions.index');
});

require __DIR__.'/auth.php';

