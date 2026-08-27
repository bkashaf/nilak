<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_blocks', function (Blueprint $table) {
            $table->id();

            // صفحه‌ای که بلوک متعلق به آن است
            $table->foreignId('page_id')
                  ->constrained('pages')
                  ->onDelete('cascade');

            // نوع بلوک (مثلاً featured-product, category-grid)
            $table->string('type', 100);

            // تنظیمات بلوک (JSON)
            $table->json('data')->nullable();

            // ترتیب نمایش بلوک‌ها در صفحه
            $table->unsignedInteger('position')->default(0);

            $table->timestamps();

            // ایندکس‌ها برای سرعت بیشتر
            $table->index(['page_id', 'position']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_blocks');
    }
};
