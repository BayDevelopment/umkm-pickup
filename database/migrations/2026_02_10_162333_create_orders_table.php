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

            $table->string('order_code')->unique();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            $table->foreignId('payment_method_id')
                ->nullable()
                ->constrained('payment_methods')
                ->nullOnDelete();

            // Snapshot pembayaran
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('payment_proof')->nullable(); // bukti transfer

            // Status
            $table->enum('status', ['pending', 'process', 'done', 'cancel'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'rejected'])->default('pending');

            $table->unsignedBigInteger('total_price')->default(0);

            $table->boolean('stock_restored')->default(false); // untuk rollback stok jika cancel

            $table->string('note')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
