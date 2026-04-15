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

            // RELASI ORDER
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            // RELASI VARIANT (FIX)
            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();

            // QTY (AMAN)
            $table->unsignedInteger('quantity');

            // HARGA SNAPSHOT
            $table->unsignedBigInteger('price');

            // SUBTOTAL SNAPSHOT
            $table->unsignedBigInteger('subtotal');

            // SNAPSHOT PRODUK
            $table->string('product_name');
            $table->string('variant_sku')->nullable();
            $table->string('variant_color')->nullable();
            $table->string('variant_size')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();

            // INDEX
            $table->index(['order_id', 'product_variant_id']);

            // OPTIONAL: anti duplicate dalam 1 order
            $table->unique(['order_id', 'product_variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
