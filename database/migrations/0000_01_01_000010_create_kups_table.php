<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: kups
|--------------------------------------------------------------------------
| Stores KUPS (Kelompok Usaha Perhutanan Sosial) records.
| id_kelompok references kelompok.id (no FK constraint - documentation only).
| Tracks business units, production, and classification.
| No timestamps on this table.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kups', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('id_kelompok'); // ref: kelompok.id
            $table->string('nama_kups', 200)->nullable();
            $table->string('ketua_kups', 200)->nullable();
            $table->integer('jumlah_anggota')->nullable();
            $table->enum('kelas_kups', ['Biru', 'Perak', 'Emas', 'Platinum'])->nullable();
            $table->year('tahun_bentuk')->nullable();
            $table->string('jenis_usaha', 200)->nullable();
            $table->string('produk', 200)->nullable();
            $table->integer('produksi_tahun')->nullable();
            $table->string('satuan_produksi', 50)->nullable();
            $table->text('keterangan')->nullable();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kups');
    }
};
