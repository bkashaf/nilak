<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->unique(['product_id', 'locale']);
        });

        DB::table('products')->select(['id', 'name', 'short_description', 'description'])->orderBy('id')->each(function ($product) {
            DB::table('product_translations')->insert([
                'product_id' => $product->id,
                'locale' => 'fa',
                'name' => $product->name,
                'short_description' => $product->short_description,
                'description' => $product->description,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_translations');
    }
};
