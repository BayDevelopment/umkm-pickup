<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkms', function (Blueprint $table) {
            $table->id();

            // DATA UMKM
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('city')->nullable();

            // DATA VERIFIKASI
            $table->string('ktp_number'); // NIK
            $table->string('ktp_image');  // foto KTP
            // $table->string('selfie_image')->nullable(); // selfie + KTP

            // STATUS VERIFIKASI
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])
                ->default('pending');

            $table->text('verification_note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // INDEX
            $table->index('verification_status');
            $table->unique('ktp_number'); // 1 KTP = 1 UMKM
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};
