<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: kegiatan
|--------------------------------------------------------------------------
| Stores kegiatan (activity) records for a kelompok.
| id_kelompok references kelompok.id (no FK constraint - documentation only).
| Includes optional KML file reference for spatial activity data.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('id_kelompok'); // ref: kelompok.id
            $table->string('nama_kegiatan', 200);
            $table->date('tanggal');
            $table->string('lokasi', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('kml_file', 255)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan');
    }
};
