<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('kode_booking')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // SESUAIKAN: ganti 'area_parkirs' dengan nama tabel area parkir kamu
            $table->foreignId('area_id')->constrained('area_parkirs');

            $table->string('jenis_kendaraan');
            $table->string('plat_nomor');
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->unsignedInteger('estimasi_biaya')->default(0);

            // dipesan, parkir, selesai, batal, hangus
            $table->string('status')->default('dipesan');

            $table->timestamps();

            $table->index(['area_id', 'waktu_mulai', 'waktu_selesai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
