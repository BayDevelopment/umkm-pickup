<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            // RELASI KE CART
            $table->foreignId('cart_id')
                ->constrained()
                ->cascadeOnDelete();

            // RELASI KE VARIANT
            $table->foreignId('variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();

            // QTY (TIDAK BOLEH NEGATIF)
            $table->unsignedInteger('qty');

            // SNAPSHOT HARGA (WAJIB)
            $table->unsignedBigInteger('price');

            $table->timestamps();

            // ANTI DUPLIKAT ITEM
            $table->unique(['cart_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
