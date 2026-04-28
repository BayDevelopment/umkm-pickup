<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();

            $table->unsignedInteger('quantity');

            // Snapshot harga
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('subtotal');

            // Snapshot produk
            $table->string('product_name');
            $table->string('variant_sku')->nullable();

            // ✅ Ganti color/size → attributes JSON (dinamis)
            $table->json('variant_attributes')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'product_variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
