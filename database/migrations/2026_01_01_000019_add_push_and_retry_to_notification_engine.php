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
        Schema::table('notification_preferences', function (Blueprint $table) {
            if (! Schema::hasColumn('notification_preferences', 'channel_push')) {
                $table->boolean('channel_push')->default(true)->after('channel_whatsapp');
            }
        });

        Schema::table('communication_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('communication_logs', 'retry_count')) {
                $table->unsignedInteger('retry_count')->default(0)->after('failure_reason');
                $table->unsignedInteger('max_retries')->default(3)->after('retry_count');
                $table->timestamp('last_attempt_at')->nullable()->after('max_retries');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('communication_logs', function (Blueprint $table) {
            $table->dropColumn(['retry_count', 'max_retries', 'last_attempt_at']);
        });

        Schema::table('notification_preferences', function (Blueprint $table) {
            $table->dropColumn('channel_push');
        });
    }
};
