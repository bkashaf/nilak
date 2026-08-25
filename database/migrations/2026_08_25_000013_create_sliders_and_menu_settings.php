<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('key')->default('home_hero')->index();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('image_path')->nullable();
            $table->string('link_url')->nullable();
            $table->string('link_text')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'group')) {
                $table->string('group')->default('general')->after('key');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sliders');

        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'group')) {
                $table->dropColumn('group');
            }
        });
    }
};
