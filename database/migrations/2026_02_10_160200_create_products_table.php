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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // RELASI UMKM (WAJIB 🔥)
            $table->foreignId('umkm_id')
                ->constrained()
                ->cascadeOnDelete();

            // RELASI
            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // DATA UTAMA
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['food', 'drink', 'fashion']); // 🔥 penting

            $table->text('description')->nullable();

            // STATUS
            $table->boolean('is_active')->default(true);

            // INDEX (biar cepat query)
            $table->index('category_id');
            $table->index('type');
            $table->index('is_active');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
