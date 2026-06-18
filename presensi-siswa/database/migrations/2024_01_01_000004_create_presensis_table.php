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
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpa'])->default('hadir');
            $table->text('keterangan')->nullable();
            $table->string('lokasi')->nullable(); // untuk menyimpan lokasi GPS jika diperlukan
            $table->timestamps();

            // Index untuk query yang lebih cepat
            $table->index(['tanggal', 'siswa_id']);
            $table->unique(['tanggal', 'siswa_id']); // satu siswa hanya bisa presensi sekali per hari
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
