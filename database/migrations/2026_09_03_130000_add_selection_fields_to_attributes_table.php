<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attributes', function (Blueprint $table) {
            $table->string('selection_mode')->default('single')->after('type');
            $table->string('display_mode')->default('dropdown')->after('selection_mode');
            $table->json('config')->nullable()->after('display_mode');
        });

        DB::table('attributes')
            ->select(['id', 'slug', 'type'])
            ->orderBy('id')
            ->get()
            ->each(function ($attribute) {
                $selectionMode = 'single';
                $displayMode = 'dropdown';

                if (in_array($attribute->slug, ['color', 'colour', 'size'], true)) {
                    $selectionMode = 'multiple';
                }

                if (in_array($attribute->slug, ['color', 'colour'], true)) {
                    $displayMode = 'swatch';
                } elseif ($attribute->slug === 'size') {
                    $displayMode = 'chip';
                } elseif ($attribute->type === 'boolean') {
                    $displayMode = 'toggle';
                }

                DB::table('attributes')
                    ->where('id', $attribute->id)
                    ->update([
                        'selection_mode' => $selectionMode,
                        'display_mode' => $displayMode,
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('attributes', function (Blueprint $table) {
            $table->dropColumn(['selection_mode', 'display_mode', 'config']);
        });
    }
};