<?php

namespace Tests\Feature;

use App\Core\Services\DigitalDocumentService;
use App\Livewire\Admin\DocumentVault;
use App\Models\AdmissionLead;
use App\Models\DigitalDocument;
use App\Models\DocumentActivityLog;
use App\Models\DocumentVersion;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class DigitalDocumentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $otherSchoolUser;
    protected School $school;
    protected School $otherSchool;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->school = School::first() ?? School::create(['name' => 'Oakridge Intl Academy', 'code' => 'OIA-01']);
        $this->otherSchool = School::create(['name' => 'St. Xavier School', 'code' => 'STX-02']);

        $this->adminUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Document Vault Admin',
            'email' => 'doc.admin@smartschool.edu',
            'password' => bcrypt('password123'),
        ]);
        $this->adminUser->assignRole('super-admin');

        $this->otherSchoolUser = User::create([
            'school_id' => $this->otherSchool->id,
            'name' => 'Other School Admin',
            'email' => 'other.admin@smartschool.edu',
            'password' => bcrypt('password123'),
        ]);

        Storage::fake('local');
    }

    public function test_authorized_admin_can_access_document_vault_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('documents.vault'));
        $response->assertStatus(200);
        $response->assertSee('Digital Document Vault');
    }

    public function test_digital_document_service_stores_file_in_private_storage_with_audit_trail(): void
    {
        $service = app(DigitalDocumentService::class);
        $this->actingAs($this->adminUser);

        $fakeFile = UploadedFile::fake()->create('birth_certificate.pdf', 500, 'application/pdf');

        $document = $service->storeDocument([
            'school_id' => $this->school->id,
            'title' => 'Student Birth Certificate',
            'category' => 'birth_certificate',
            'is_confidential' => false,
        ], $fakeFile);

        $this->assertInstanceOf(DigitalDocument::class, $document);
        $this->assertEquals(1, $document->version);
        $this->assertStringContainsString('private_documents', $document->file_path);

        // Assert file exists in private storage disk
        Storage::disk('local')->assertExists($document->file_path);

        // Assert Version 1 record created
        $this->assertDatabaseHas('document_versions', [
            'document_id' => $document->id,
            'version_number' => 1,
        ]);

        // Assert Activity Log recorded
        $this->assertDatabaseHas('document_activity_logs', [
            'document_id' => $document->id,
            'activity_type' => 'uploaded',
        ]);
    }

    public function test_add_version_increments_version_number_and_updates_file_pointer(): void
    {
        $service = app(DigitalDocumentService::class);
        $this->actingAs($this->adminUser);

        $fileV1 = UploadedFile::fake()->create('doc_v1.pdf', 300, 'application/pdf');
        $document = $service->storeDocument([
            'school_id' => $this->school->id,
            'title' => 'Teacher Employment Contract',
            'category' => 'employment_contract',
        ], $fileV1);

        $fileV2 = UploadedFile::fake()->create('doc_v2_updated.pdf', 450, 'application/pdf');
        $newVersion = $service->addVersion($document, $fileV2, 'Updated salary annexure');

        $document->refresh();

        $this->assertEquals(2, $document->version);
        $this->assertEquals('doc_v2_updated.pdf', $document->file_name);
        $this->assertEquals(2, $newVersion->version_number);

        $this->assertDatabaseHas('document_activity_logs', [
            'document_id' => $document->id,
            'activity_type' => 'version_added',
        ]);
    }

    public function test_update_verification_status_records_verifier_and_audit_trail(): void
    {
        $service = app(DigitalDocumentService::class);
        $this->actingAs($this->adminUser);

        $fakeFile = UploadedFile::fake()->create('report_card.pdf', 200, 'application/pdf');
        $document = $service->storeDocument([
            'school_id' => $this->school->id,
            'title' => 'Grade 10 Marksheet',
            'category' => 'academic_transcript',
        ], $fakeFile);

        $service->updateVerificationStatus($document, 'verified', 'All seal marks verified.');

        $document->refresh();

        $this->assertEquals('verified', $document->verification_status);
        $this->assertEquals($this->adminUser->id, $document->verified_by);

        $this->assertDatabaseHas('document_activity_logs', [
            'document_id' => $document->id,
            'activity_type' => 'verification_changed',
        ]);
    }

    public function test_document_streaming_preview_and_download_routes_enforce_security_and_audit_log(): void
    {
        $service = app(DigitalDocumentService::class);
        $this->actingAs($this->adminUser);

        $fakeFile = UploadedFile::fake()->create('confidential_memo.pdf', 150, 'application/pdf');
        $document = $service->storeDocument([
            'school_id' => $this->school->id,
            'title' => 'Confidential Board Memo',
            'category' => 'general',
            'is_confidential' => true,
        ], $fakeFile);

        // 1. Test Preview Route
        $previewResponse = $this->actingAs($this->adminUser)->get(route('documents.preview', $document->id));
        $previewResponse->assertStatus(200);
        $previewResponse->assertHeader('Content-Type', 'application/pdf');

        $this->assertDatabaseHas('document_activity_logs', [
            'document_id' => $document->id,
            'activity_type' => 'previewed',
        ]);

        // 2. Test Download Route
        $downloadResponse = $this->actingAs($this->adminUser)->get(route('documents.download', $document->id));
        $downloadResponse->assertStatus(200);

        $this->assertDatabaseHas('document_activity_logs', [
            'document_id' => $document->id,
            'activity_type' => 'downloaded',
        ]);

        // 3. Test Unauthorized Tenant Access (Denied 403/404 due to tenant isolation)
        $unauthorizedResponse = $this->actingAs($this->otherSchoolUser)->get(route('documents.preview', $document->id));
        $this->assertTrue(in_array($unauthorizedResponse->status(), [403, 404]));
    }
}
