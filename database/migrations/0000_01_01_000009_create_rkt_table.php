<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: rkt
|--------------------------------------------------------------------------
| Stores RKT (Rencana Kerja Tahunan) master records.
| id_kelompok references kelompok.id (no FK constraint - documentation only).
| id_rkps references rkps.id_rkps (no FK constraint - documentation only).
| Primary key is id_rkt.
| Only created_at timestamp; no updated_at.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rkt', function (Blueprint $table) {
            $table->integer('id_rkt')->autoIncrement();
            $table->integer('id_kelompok')->nullable(); // ref: kelompok.id
            $table->integer('id_rkps')->nullable(); // ref: rkps.id_rkps
            $table->year('tahun')->nullable();
            $table->string('file_rkt', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->string('no_penetapan', 100)->nullable();
            $table->date('tgl_penetapan')->nullable();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rkt');
    }
};
