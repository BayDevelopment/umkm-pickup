<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // contoh: Transfer Bank, E-Wallet
            $table->string('bank_name')->nullable(); // hanya untuk bank
            $table->string('account_number')->unique(); // wajib & unik
            $table->string('account_name'); // wajib

            $table->boolean('is_active')->default(true);

            $table->softDeletes();
            $table->timestamps();

            // Index untuk performa
            $table->index('is_active');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
