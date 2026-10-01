<?php

namespace App\Providers;

use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Division;
use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use App\Policies\AcademicYearPolicy;
use App\Policies\CampusPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\DivisionPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\SchoolClassPolicy;
use App\Policies\SchoolPolicy;
use App\Policies\SubjectPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Policy registrations
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(Organization::class, OrganizationPolicy::class);
        Gate::policy(School::class, SchoolPolicy::class);
        Gate::policy(Campus::class, CampusPolicy::class);
        Gate::policy(AcademicYear::class, AcademicYearPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(SchoolClass::class, SchoolClassPolicy::class);
        Gate::policy(Division::class, DivisionPolicy::class);
        Gate::policy(Subject::class, SubjectPolicy::class);
        Gate::policy(\App\Models\Student::class, \App\Policies\StudentPolicy::class);
        Gate::policy(\App\Models\ParentProfile::class, \App\Policies\ParentPolicy::class);
        Gate::policy(\App\Models\StaffProfile::class, \App\Policies\StaffPolicy::class);
        Gate::policy(\App\Models\AdmissionLead::class, \App\Policies\AdmissionPolicy::class);
        Gate::policy(\App\Models\Communication::class, \App\Policies\CommunicationPolicy::class);
        Gate::policy(\App\Models\CrmTask::class, \App\Policies\CrmTaskPolicy::class);
        Gate::policy(\App\Models\Assessment::class, \App\Policies\AssessmentPolicy::class);
        Gate::policy(\App\Models\AssessmentQuestion::class, \App\Policies\AssessmentQuestionPolicy::class);
        Gate::policy(\App\Models\AssessmentAttempt::class, \App\Policies\AssessmentAttemptPolicy::class);

        // Super Admin Gate Bypass
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('super-admin')) {
                return true;
            }
        });
    }
}
