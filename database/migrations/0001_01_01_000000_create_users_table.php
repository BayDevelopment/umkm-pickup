<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password');

            // ROLE
            $table->enum('role', ['customer', 'admin', 'owner'])
                ->default('customer');

            // STATUS AKUN (PENTING BANGET)
            $table->enum('status', ['pending', 'active', 'suspended'])
                ->default('pending');

            // NOMOR HP
            $table->string('phone')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // INDEX
            $table->index(['role', 'status']);
        });

        // RESET PASSWORD
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // SESSIONS
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');

            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
