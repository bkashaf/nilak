<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->unique(['category_id', 'locale']);
        });

        DB::table('categories')->select(['id', 'name', 'description'])->orderBy('id')->each(function ($category) {
            DB::table('category_translations')->insert([
                'category_id' => $category->id,
                'locale' => 'fa',
                'name' => $category->name,
                'description' => $category->description,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_translations');
    }
};
