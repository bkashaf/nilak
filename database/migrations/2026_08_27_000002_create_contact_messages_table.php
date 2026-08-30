<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();

            // فرم تماس ادمین (قبلی)
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('subject')->nullable();

            // فرم تماس فرانت (جدید)
            $table->string('name')->nullable();
            $table->string('email')->nullable();

            // مشترک
            $table->text('message');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
