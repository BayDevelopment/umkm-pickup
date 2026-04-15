<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // RELASI USER
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // TOTAL HARGA (TIDAK BOLEH NEGATIF)
            $table->unsignedBigInteger('total_price')->default(0);

            // PAYMENT METHOD
            $table->foreignId('payment_method_id')
                ->nullable()
                ->constrained('payment_methods')
                ->nullOnDelete();

            // SNAPSHOT DATA BANK (biar tidak berubah kalau payment method di edit)
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();

            // BUKTI TRANSFER
            $table->string('payment_proof')->nullable();

            // STATUS PEMBAYARAN (ENUM - sesuai request)
            $table->enum('payment_status', ['pending', 'paid', 'rejected'])
                ->default('pending');

            // STATUS ORDER (ENUM - sesuai request)
            $table->enum('status', ['pending', 'process', 'done', 'cancel'])
                ->default('pending');

            $table->softDeletes();
            $table->timestamps();

            // INDEXING (BIAR CEPAT QUERY)
            $table->index(['user_id', 'status']);
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
