<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Permissions grouped by module
        $permissions = [
            // User management
            ['name' => 'View Users', 'slug' => 'users.view', 'group' => 'users', 'description' => 'Can view users list and details'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'group' => 'users', 'description' => 'Can create new users'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'group' => 'users', 'description' => 'Can edit user details and status'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'group' => 'users', 'description' => 'Can delete user accounts'],
            ['name' => 'Manage User Roles', 'slug' => 'users.manage-roles', 'group' => 'users', 'description' => 'Can assign and sync user roles'],

            // Roles & Permissions management
            ['name' => 'View Roles', 'slug' => 'roles.view', 'group' => 'roles', 'description' => 'Can view roles list'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'group' => 'roles', 'description' => 'Can create, edit, and assign permissions to roles'],
            ['name' => 'View Permissions', 'slug' => 'permissions.view', 'group' => 'permissions', 'description' => 'Can view system permissions'],
            ['name' => 'Manage Permissions', 'slug' => 'permissions.manage', 'group' => 'permissions', 'description' => 'Can create and edit permissions'],

            // Student management
            ['name' => 'View Students', 'slug' => 'students.view', 'group' => 'students', 'description' => 'Can view student directory'],
            ['name' => 'Create Students', 'slug' => 'students.create', 'group' => 'students', 'description' => 'Can enroll new students'],
            ['name' => 'Edit Students', 'slug' => 'students.edit', 'group' => 'students', 'description' => 'Can update student profiles'],
            ['name' => 'Delete Students', 'slug' => 'students.delete', 'group' => 'students', 'description' => 'Can archive or delete student records'],

            // Parent & Family management
            ['name' => 'View Parents', 'slug' => 'parents.view', 'group' => 'parents', 'description' => 'Can view parent directory and 360 profiles'],
            ['name' => 'Create Parents', 'slug' => 'parents.create', 'group' => 'parents', 'description' => 'Can add parent and guardian records'],
            ['name' => 'Edit Parents', 'slug' => 'parents.edit', 'group' => 'parents', 'description' => 'Can edit parent profiles and student linkages'],
            ['name' => 'Delete Parents', 'slug' => 'parents.delete', 'group' => 'parents', 'description' => 'Can archive or remove parent records'],

            // Staff & Faculty
            ['name' => 'View Staff', 'slug' => 'staff.view', 'group' => 'staff', 'description' => 'Can view staff directory and profile 360'],
            ['name' => 'Create Staff', 'slug' => 'staff.create', 'group' => 'staff', 'description' => 'Can enroll new teacher and staff records'],
            ['name' => 'Edit Staff', 'slug' => 'staff.edit', 'group' => 'staff', 'description' => 'Can update staff profiles and designations'],
            ['name' => 'Delete Staff', 'slug' => 'staff.delete', 'group' => 'staff', 'description' => 'Can archive or terminate staff profiles'],
            ['name' => 'Assign Staff', 'slug' => 'staff.assign', 'group' => 'staff', 'description' => 'Can assign subjects and classes to teachers'],
            // Admissions & Lead Management
            ['name' => 'View Admissions', 'slug' => 'admissions.view', 'group' => 'admissions', 'description' => 'Can view admission leads, pipeline, and dashboards'],
            ['name' => 'Create Admissions', 'slug' => 'admissions.create', 'group' => 'admissions', 'description' => 'Can create new admission leads and applications'],
            ['name' => 'Edit Admissions', 'slug' => 'admissions.edit', 'group' => 'admissions', 'description' => 'Can update lead stages, inquiries, and application notes'],
            ['name' => 'Delete Admissions', 'slug' => 'admissions.delete', 'group' => 'admissions', 'description' => 'Can archive or delete lead records'],
            ['name' => 'Assign Admissions', 'slug' => 'admissions.assign', 'group' => 'admissions', 'description' => 'Can reassign counselors to admission leads'],

            // Communication CRM System
            ['name' => 'View Communications', 'slug' => 'communications.view', 'group' => 'communications', 'description' => 'Can view communication dashboard, logs, and templates'],
            ['name' => 'Send Communications', 'slug' => 'communications.send', 'group' => 'communications', 'description' => 'Can compose and send individual or broadcast messages'],
            ['name' => 'Schedule Communications', 'slug' => 'communications.schedule', 'group' => 'communications', 'description' => 'Can schedule delayed broadcast communications'],
            ['name' => 'Manage Templates', 'slug' => 'communications.templates', 'group' => 'communications', 'description' => 'Can create and edit message templates'],

            // Academics & Grading
            ['name' => 'View Academics', 'slug' => 'academics.view', 'group' => 'academics', 'description' => 'Can view academic classes and schedules'],
            ['name' => 'Manage Academics', 'slug' => 'academics.manage', 'group' => 'academics', 'description' => 'Can create classes, subjects, and gradebooks'],

            // Finance & Billing
            ['name' => 'View Finance', 'slug' => 'finance.view', 'group' => 'finance', 'description' => 'Can view fee invoices and financial statements'],
            ['name' => 'Manage Finance', 'slug' => 'finance.manage', 'group' => 'finance', 'description' => 'Can issue invoices, process fee payments'],

            // Reports & Analytics
            ['name' => 'View Reports', 'slug' => 'reports.view', 'group' => 'reports', 'description' => 'Can view CRM reports'],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'group' => 'reports', 'description' => 'Can export analytical reports'],

            // Settings
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'group' => 'settings', 'description' => 'Can modify system settings'],

            // CRM Task & Follow-up Engine
            ['name' => 'View Tasks', 'slug' => 'tasks.view', 'group' => 'tasks', 'description' => 'Can view task dashboard and task workspaces'],
            ['name' => 'Create Tasks', 'slug' => 'tasks.create', 'group' => 'tasks', 'description' => 'Can create tasks and attach to entities'],
            ['name' => 'Edit Tasks', 'slug' => 'tasks.edit', 'group' => 'tasks', 'description' => 'Can update task details, priority, and status'],
            ['name' => 'Delete Tasks', 'slug' => 'tasks.delete', 'group' => 'tasks', 'description' => 'Can archive or delete tasks'],
            ['name' => 'Assign Tasks', 'slug' => 'tasks.assign', 'group' => 'tasks', 'description' => 'Can assign tasks to staff members'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['slug' => $p['slug']], $p);
        }

        // 2. Create Explicit School CRM Roles
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full unrestricted system-wide administrative control.',
            ],
            [
                'name' => 'School Admin',
                'slug' => 'school-admin',
                'description' => 'Full administrative access within assigned school tenant.',
            ],
            [
                'name' => 'Principal',
                'slug' => 'principal',
                'description' => 'Executive school principal with academic and oversight permissions.',
            ],
            [
                'name' => 'Teacher',
                'slug' => 'teacher',
                'description' => 'Faculty member managing classroom academics, attendance, and student grades.',
            ],
            [
                'name' => 'Counselor',
                'slug' => 'counselor',
                'description' => 'Student counselor managing student counseling notes and guidance.',
            ],
            [
                'name' => 'Admission Staff',
                'slug' => 'admission-staff',
                'description' => 'Staff handling student inquiries, applications, and enrollments.',
            ],
            [
                'name' => 'Accountant',
                'slug' => 'accountant',
                'description' => 'Financial accountant managing student fee billing and collections.',
            ],
            [
                'name' => 'Parent',
                'slug' => 'parent',
                'description' => 'Parent/Guardian portal access to track linked children.',
            ],
            [
                'name' => 'Student',
                'slug' => 'student',
                'description' => 'Student portal access for class schedule, assignments, and grades.',
            ],
        ];

        foreach ($roles as $r) {
            $role = Role::firstOrCreate(['slug' => $r['slug']], $r);

            // Assign permission sets per role
            if ($r['slug'] === 'super-admin' || $r['slug'] === 'school-admin') {
                $role->permissions()->sync(Permission::all());
            } elseif ($r['slug'] === 'principal') {
                $role->permissions()->sync(Permission::whereIn('group', ['users', 'students', 'staff', 'academics', 'finance', 'reports'])->pluck('id'));
            } elseif ($r['slug'] === 'teacher') {
                $role->permissions()->sync(Permission::whereIn('slug', ['students.view', 'students.edit', 'academics.view', 'academics.manage', 'reports.view'])->pluck('id'));
            } elseif ($r['slug'] === 'counselor') {
                $role->permissions()->sync(Permission::whereIn('slug', ['students.view', 'reports.view'])->pluck('id'));
            } elseif ($r['slug'] === 'admission-staff') {
                $role->permissions()->sync(Permission::whereIn('slug', ['students.view', 'students.create', 'students.edit'])->pluck('id'));
            } elseif ($r['slug'] === 'accountant') {
                $role->permissions()->sync(Permission::whereIn('group', ['finance', 'reports'])->pluck('id'));
            } elseif ($r['slug'] === 'parent' || $r['slug'] === 'student') {
                $role->permissions()->sync(Permission::whereIn('slug', ['students.view', 'academics.view'])->pluck('id'));
            }
        }
    }
}
