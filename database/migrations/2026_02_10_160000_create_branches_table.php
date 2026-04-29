<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');                   // "Outlet Pondok Indah", "Cabang Kelapa Gading"
            $table->string('slug')->unique();         // pondok-indah, kelapa-gading
            $table->text('address')->nullable();
            $table->string('city')->nullable();       // Jakarta Selatan, Tangerang, dll
            $table->string('district')->nullable();   // Kec. Pondok Aren
            $table->string('subdistrict')->nullable(); // Kel. Pondok Jaya
            $table->string('postal_code', 10)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('phone', 20)->nullable();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->string('image')->nullable();      // foto depan toko
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // index biar query kota/kec cepet
            $table->index(['city', 'district']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
