<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: pengajuan_rkps
|--------------------------------------------------------------------------
| Stores RKPS (Rencana Kerja Pembangunan Hutan) submission records.
| id_kelompok references kelompok.id (no FK constraint - documentation only).
| Uses tanggal_pengajuan as the primary timestamp; no created_at/updated_at.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_rkps', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('id_kelompok'); // ref: kelompok.id
            $table->integer('tahun_mulai');
            $table->integer('tahun_selesai');
            $table->string('file_rkps', 255);
            $table->enum('status', ['Menunggu', 'Revisi', 'Disetujui', 'Ditolak'])->nullable()->default('Menunggu');
            $table->text('catatan_admin')->nullable();
            $table->timestamp('tanggal_pengajuan')->useCurrent();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_rkps');
    }
};
