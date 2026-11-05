<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: rkps
|--------------------------------------------------------------------------
| Stores RKPS (Rencana Kerja Pembangunan Hutan) master records.
| id_kelompok references kelompok.id (no FK constraint - documentation only).
| Primary key is id_rkps.
| Only created_at timestamp; no updated_at.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rkps', function (Blueprint $table) {
            $table->integer('id_rkps')->autoIncrement();
            $table->integer('id_kelompok')->nullable(); // ref: kelompok.id
            $table->year('tahun_mulai')->nullable();
            $table->year('tahun_selesai')->nullable();
            $table->string('no_penilaian', 100)->nullable();
            $table->date('tgl_penilaian')->nullable();
            $table->string('no_pengesahan', 100)->nullable();
            $table->date('tgl_pengesahan')->nullable();
            $table->string('file_rkps', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rkps');
    }
};
