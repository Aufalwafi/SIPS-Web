<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: permasalahan_kelompok
|--------------------------------------------------------------------------
| Stores problem/challenge records reported by kelompok.
| id_kelompok references kelompok.id (no FK constraint - documentation only).
| Categorizes issues by type, severity, impact, and needed resources.
| Only created_at timestamp; no updated_at.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permasalahan_kelompok', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('id_kelompok'); // ref: kelompok.id
            $table->string('kategori', 100);
            $table->text('uraian');
            $table->enum('tingkat', ['Ringan', 'Sedang', 'Berat'])->nullable();
            $table->text('dampak')->nullable();
            $table->text('kebutuhan')->nullable();
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
        Schema::dropIfExists('permasalahan_kelompok');
    }
};
