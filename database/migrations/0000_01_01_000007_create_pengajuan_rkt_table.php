<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: pengajuan_rkt
|--------------------------------------------------------------------------
| Stores RKT (Rencana Kerja Tahunan) submission records.
| id_kelompok references kelompok.id (no FK constraint - documentation only).
| No timestamps beyond tanggal_pengajuan.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_rkt', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('id_kelompok')->nullable(); // ref: kelompok.id
            $table->integer('tahun')->nullable();
            $table->string('file_rkt', 255)->nullable();
            $table->enum('status', ['Menunggu', 'Revisi', 'Disetujui', 'Ditolak'])->nullable()->default('Menunggu');
            $table->text('catatan_admin')->nullable();
            $table->dateTime('tanggal_pengajuan')->nullable();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_rkt');
    }
};
