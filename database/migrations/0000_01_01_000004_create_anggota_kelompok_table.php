<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: anggota_kelompok
|--------------------------------------------------------------------------
| Stores individual member records within a kelompok.
| id_kelompok references kelompok.id (no FK constraint - documentation only).
| Contains personal info and land/crop details.
| No timestamps on this table.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_kelompok', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('id_kelompok')->nullable(); // ref: kelompok.id
            $table->string('nama_anggota', 100)->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->decimal('luas_garapan', 6, 2)->nullable();
            $table->string('komoditas_utama', 100)->nullable();
            $table->text('polygon')->nullable();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_kelompok');
    }
};
