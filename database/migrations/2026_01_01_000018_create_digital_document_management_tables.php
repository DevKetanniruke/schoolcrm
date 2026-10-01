<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Digital Documents Core Table
        Schema::create('digital_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            
            // Polymorphic relation to Student, ParentProfile, StaffProfile, AdmissionLead, School, AdmissionApplication, ParentComplaint
            $table->nullableMorphs('documentable');
            
            $table->string('title');
            $table->string('category')->default('general'); 
            // Categories: birth_certificate, id_proof, academic_transcript, medical_record, employment_contract, application_form, complaint_evidence, general
            
            $table->string('file_path'); // Stored in private non-public storage
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type')->default('application/pdf');
            $table->unsignedInteger('version')->default(1);
            
            $table->date('expiry_date')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected', 'expired'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            
            $table->date('retention_until_date')->nullable();
            $table->boolean('is_confidential')->default(false);
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'category']);
            $table->index(['verification_status', 'expiry_date']);
        });

        // 2. Document Versions Table
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('digital_documents')->cascadeOnDelete();
            $table->unsignedInteger('version_number');
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type')->default('application/pdf');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['document_id', 'version_number']);
        });

        // 3. Document Activity Audit Logs Table
        Schema::create('document_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('digital_documents')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('activity_type'); // uploaded, downloaded, previewed, version_added, verification_changed, retention_updated, deleted
            $table->string('description');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['document_id', 'activity_type']);
        });

        // 4. Configurable Document Retention Policies Table
        Schema::create('document_retention_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('document_category');
            $table->unsignedInteger('retention_period_months')->default(36);
            $table->boolean('auto_archive')->default(true);
            $table->boolean('auto_delete')->default(false);
            $table->timestamps();

            $table->unique(['school_id', 'document_category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_retention_policies');
        Schema::dropIfExists('document_activity_logs');
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('digital_documents');
    }
};
