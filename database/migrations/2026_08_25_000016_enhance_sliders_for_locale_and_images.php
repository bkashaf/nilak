<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->string('locale', 5)->nullable()->after('key');
            $table->string('description', 255)->nullable()->after('subtitle');
            $table->string('mobile_image_path')->nullable()->after('image_path');
            $table->unsignedTinyInteger('focal_x')->default(50)->after('link_text');
            $table->unsignedTinyInteger('focal_y')->default(50)->after('focal_x');
            $table->unsignedTinyInteger('mobile_focal_x')->default(50)->after('focal_y');
            $table->unsignedTinyInteger('mobile_focal_y')->default(50)->after('mobile_focal_x');
        });
    }

    public function down(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn([
                'locale',
                'description',
                'mobile_image_path',
                'focal_x',
                'focal_y',
                'mobile_focal_x',
                'mobile_focal_y',
            ]);
        });
    }
};
