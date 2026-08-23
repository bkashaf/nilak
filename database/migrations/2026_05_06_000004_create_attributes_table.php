<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // نام ویژگی (مثلاً رنگ، اندازه)
            $table->string('slug')->unique();  // slug برای فیلتر/SEO در صورت نیاز
            $table->string('type')->default('text'); // نوع ورودی: text, select, boolean, number, etc.
            $table->boolean('is_filterable')->default(false); // آیا در فیلترها نمایش داده شود
            $table->boolean('is_required')->default(false);
            $table->integer('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
