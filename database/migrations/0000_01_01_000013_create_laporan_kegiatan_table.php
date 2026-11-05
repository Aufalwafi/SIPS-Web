<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: laporan_kegiatan
|--------------------------------------------------------------------------
| Stores activity reports (laporan kegiatan) for kelompok.
| id_kelompok references kelompok.id (no FK constraint - documentation only).
| id_user references users.id (no FK constraint - documentation only).
| role_pembuat indicates the role of the report creator.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_kegiatan', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('id_kelompok'); // ref: kelompok.id
            $table->integer('id_user'); // ref: users.id
            $table->string('role_pembuat', 20);
            $table->date('tanggal');
            $table->string('kegiatan', 200);
            $table->text('deskripsi')->nullable();
            $table->string('foto', 200)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kegiatan');
    }
};
