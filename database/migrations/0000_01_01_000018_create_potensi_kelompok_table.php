<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: potensi_kelompok
|--------------------------------------------------------------------------
| Stores potential/resource records (kayu, non-kayu, jasa lingkungan) per kelompok.
| id_kelompok references kelompok.id (no FK constraint - documentation only).
| Tracks types of forest resources, planting year, area, and condition.
| Only created_at timestamp; no updated_at.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('potensi_kelompok', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('id_kelompok'); // ref: kelompok.id
            $table->enum('jenis_potensi', ['Kayu', 'Non Kayu', 'Jasa Lingkungan']);
            $table->string('jenis_detail', 150);
            $table->year('tahun_tanam')->nullable();
            $table->decimal('luasan', 12, 2)->nullable();
            $table->string('satuan', 30)->nullable();
            $table->string('kondisi', 100)->nullable();
            $table->string('lokasi', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('foto', 255)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('potensi_kelompok');
    }
};
