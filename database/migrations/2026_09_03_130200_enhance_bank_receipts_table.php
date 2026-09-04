<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_receipts', function (Blueprint $table) {
            $table->string('tracking_number', 100)->nullable()->after('payment_id');
            $table->text('note')->nullable()->after('tracking_number');
            $table->string('original_name')->nullable()->after('file_path');
            $table->foreignId('uploaded_by')->nullable()->after('original_name')->constrained('users')->nullOnDelete();
            $table->timestamp('uploaded_at')->nullable()->after('uploaded_by');
            $table->string('file_path')->nullable()->change();
            $table->index(['payment_id', 'status'], 'bank_receipts_payment_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('bank_receipts', function (Blueprint $table) {
            $table->dropIndex('bank_receipts_payment_status_idx');
            $table->dropConstrainedForeignId('uploaded_by');
            $table->dropColumn(['tracking_number', 'note', 'original_name', 'uploaded_at']);
            $table->string('file_path')->nullable(false)->change();
        });
    }
};