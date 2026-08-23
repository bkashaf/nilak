<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payment_methods')) {
            return;
        }

        DB::table('payment_methods')
            ->where('name', 'online')
            ->update([
                'type' => 'gateway',
                'config' => json_encode(['gateway' => 'fake']),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('payment_methods')
            ->where('name', 'online')
            ->update([
                'type' => null,
                'config' => null,
                'updated_at' => now(),
            ]);
    }
};
