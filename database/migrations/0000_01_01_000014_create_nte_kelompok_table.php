<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: nte_kelompok
|--------------------------------------------------------------------------
| Stores NTE (Non-Timber forest products Economy) records per kelompok.
| id_kelompok references kelompok.id (no FK constraint - documentation only).
| id_kups references kups.id (no FK constraint - documentation only).
| id_pendamping references users.id (NOT the pendamping table - no FK constraint, documentation only).
| Tracks product types, processing methods, and market info.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nte_kelompok', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('id_kelompok'); // ref: kelompok.id
            $table->integer('id_kups')->nullable(); // ref: kups.id
            $table->integer('id_pendamping'); // ref: users.id (NOT pendamping table)
            $table->string('nama_produk', 150)->nullable();
            $table->string('luas_areal', 100)->nullable();
            $table->string('pola_pengolahan', 100)->nullable();
            $table->string('pola_pengelolaan', 100)->nullable();
            $table->string('potensi_jasa_lingkungan', 150)->nullable();
            $table->string('sumber_modal', 100)->nullable();
            $table->string('area_pemasaran', 100)->nullable();
            $table->integer('tahun')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->string('satuan', 50)->nullable();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nte_kelompok');
    }
};
