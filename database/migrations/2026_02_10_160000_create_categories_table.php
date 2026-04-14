<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // HIERARCHY (biar bisa parent-child kategori)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            // DATA
            $table->string('name');
            $table->string('slug')->unique();

            // OPTIONAL: icon / gambar kategori
            $table->string('image')->nullable();

            // STATUS
            $table->boolean('is_active')
                ->default(true)
                ->index();

            // INDEX
            $table->index('parent_id');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
