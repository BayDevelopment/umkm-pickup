<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            // USER LOGIN
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            // GUEST (SESSION)
            $table->string('session_id')
                ->nullable();

            // STATUS CART (BIAR BISA TRACK)
            $table->enum('status', ['active', 'checked_out'])
                ->default('active');

            $table->timestamps();

            // INDEXING
            $table->index('session_id');
            $table->index(['user_id', 'status']);

            // UNIQUE (ANTI DUPLIKAT CART AKTIF)
            $table->unique(['user_id', 'status']);
            $table->unique(['session_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
