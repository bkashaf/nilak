<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('categories')) {
            return;
        }

        $rootId = DB::table('categories')->where('slug', 'product-categories')->value('id');

        if (! $rootId) {
            $rootId = DB::table('categories')->insertGetId([
                'name' => 'دسته‌بندی محصولات',
                'slug' => 'product-categories',
                'description' => 'گروه اصلی دسته‌بندی محصولات فروشگاه',
                'parent_id' => null,
                'status' => 1,
                'position' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('categories')
            ->whereNull('parent_id')
            ->where('id', '!=', $rootId)
            ->update([
                'parent_id' => $rootId,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        $rootId = DB::table('categories')->where('slug', 'product-categories')->value('id');

        if (! $rootId) {
            return;
        }

        DB::table('categories')
            ->where('parent_id', $rootId)
            ->update(['parent_id' => null, 'updated_at' => now()]);

        DB::table('categories')->where('id', $rootId)->delete();
    }
};
