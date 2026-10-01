<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Role;
use App\Models\School;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles & Permissions Seeder
        $this->call(RolesAndPermissionsSeeder::class);

        // 2. Hierarchy Seeder
        $this->call(HierarchySeeder::class);

        // 3. Student Seeder
        $this->call(StudentSeeder::class);

        // 4. Parent & Family Seeder
        $this->call(ParentSeeder::class);

        // 5. Staff & Faculty Seeder
        $this->call(StaffSeeder::class);

        // 6. Admission CRM Seeder
        $this->call(AdmissionSeeder::class);

        // 7. Communication CRM Seeder
        $this->call(CommunicationSeeder::class);

        // 8. CRM Task Engine Seeder
        $this->call(TaskEngineSeeder::class);

        $org = Organization::first();
        $school = School::first();

        // 4. Create Super Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@smartschool.edu'],
            [
                'organization_id' => $org->id ?? null,
                'school_id' => $school->id ?? null,
                'name' => 'System Administrator',
                'phone' => '+1 (555) 987-6543',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $superAdminRole = Role::where('slug', 'super-admin')->first();
        if ($superAdminRole) {
            $admin->assignRole($superAdminRole);
        }

        // 5. Create School Admin User
        $schoolAdmin = User::firstOrCreate(
            ['email' => 'schooladmin@smartschool.edu'],
            [
                'organization_id' => $org->id ?? null,
                'school_id' => $school->id ?? null,
                'name' => 'School Admin Officer',
                'phone' => '+1 (555) 345-6789',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $schoolAdminRole = Role::where('slug', 'school-admin')->first();
        if ($schoolAdminRole) {
            $schoolAdmin->assignRole($schoolAdminRole);
        }

        // 6. System Settings
        SystemSetting::firstOrCreate(
            ['key' => 'app_theme'],
            [
                'school_id' => $school->id ?? null,
                'value' => 'light',
                'group' => 'appearance',
                'is_public' => true,
            ]
        );
    }
}
