<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {

            $table->id();

            // RELASI
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            // IDENTITAS
            $table->string('sku')->nullable()->unique();
            $table->string('name')->nullable(); // contoh: "L - Hitam" / "Ayam + Es Teh"

            // 🔥 FLEXIBLE ATTRIBUTES (pengganti color & size)
            $table->json('attributes')->nullable();

            // HARGA & STOK
            $table->unsignedInteger('price');
            $table->unsignedInteger('stock')->default(0);

            // INDEX (biar cepat)
            $table->index('product_id');
            $table->index('branch_id');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
