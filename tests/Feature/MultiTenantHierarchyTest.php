<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Division;
use App\Models\Organization;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenantHierarchyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_entity_relationships_and_cascade(): void
    {
        $org = Organization::where('code', 'OEF-GLOBAL')->first();
        $this->assertNotNull($org);

        $school = $org->schools()->first();
        $this->assertNotNull($school);

        $campus = $school->campuses()->first();
        $this->assertNotNull($campus);

        $dept = $school->departments()->where('code', 'MATH')->first();
        $this->assertNotNull($dept);

        $class = $dept->classes()->first();
        $this->assertNotNull($class);

        $division = $class->divisions()->first();
        $this->assertNotNull($division);

        $subject = $dept->subjects()->first();
        $this->assertNotNull($subject);
    }

    public function test_multi_tenant_data_isolation_scopes(): void
    {
        // Create 2 separate Organizations & Schools
        $orgA = Organization::create(['name' => 'Org A', 'code' => 'ORG-A']);
        $schoolA = School::create(['organization_id' => $orgA->id, 'name' => 'School A', 'code' => 'SCH-A']);
        $deptA = Department::create(['school_id' => $schoolA->id, 'name' => 'Dept A', 'code' => 'DA']);

        $orgB = Organization::create(['name' => 'Org B', 'code' => 'ORG-B']);
        $schoolB = School::create(['organization_id' => $orgB->id, 'name' => 'School B', 'code' => 'SCH-B']);
        $deptB = Department::create(['school_id' => $schoolB->id, 'name' => 'Dept B', 'code' => 'DB']);

        // User A belongs to School A
        $userA = User::factory()->create(['organization_id' => $orgA->id, 'school_id' => $schoolA->id]);
        $schoolAdminRole = Role::where('slug', 'school-admin')->first();
        $userA->assignRole($schoolAdminRole);

        // Under User A scope, only School A and Dept A should be visible
        $this->actingAs($userA);
        $visibleDepartments = Department::all();
        $this->assertTrue($visibleDepartments->contains($deptA));
        $this->assertFalse($visibleDepartments->contains($deptB));

        // Super Admin sees all departments across all organizations
        $superAdmin = User::where('email', 'admin@smartschool.edu')->first();
        $this->actingAs($superAdmin);
        $allDepartments = Department::all();
        $this->assertTrue($allDepartments->contains($deptA));
        $this->assertTrue($allDepartments->contains($deptB));
    }

    public function test_api_v1_organization_crud_search_pagination(): void
    {
        $superAdmin = User::where('email', 'admin@smartschool.edu')->first();

        // 1. Create Organization via API
        $response = $this->actingAs($superAdmin, 'sanctum')
            ->postJson('/api/v1/organizations', [
                'name' => 'Apex Education Trust',
                'code' => 'APEX-01',
                'contact_email' => 'contact@apex.edu',
                'status' => 'active',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'code' => 'APEX-01',
                ],
            ]);

        // 2. Search & Paginate Organizations via API
        $searchResponse = $this->actingAs($superAdmin, 'sanctum')
            ->getJson('/api/v1/organizations?search=Apex&per_page=5');

        $searchResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => ['current_page', 'total', 'per_page', 'last_page'],
            ]);

        $this->assertEquals(1, count($searchResponse->json('data')));
    }

    public function test_api_v1_school_and_campus_rest_endpoints(): void
    {
        $superAdmin = User::where('email', 'admin@smartschool.edu')->first();
        $school = School::where('code', 'OIA-01')->first();

        // List campuses filtered by school_id
        $response = $this->actingAs($superAdmin, 'sanctum')
            ->getJson('/api/v1/campuses?school_id=' . $school->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertGreaterThanOrEqual(1, count($response->json('data')));
    }
}
