<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_price_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal('old_price', 12, 2)->nullable();
            $table->decimal('new_price', 12, 2)->nullable();

            $table->decimal('old_compare_price', 12, 2)->nullable();
            $table->decimal('new_compare_price', 12, 2)->nullable();

            $table->string('change_type', 30)->default('manual');
            $table->string('reason', 255)->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['product_id', 'created_at']);
            $table->index('change_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_price_histories');
    }
};