<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: petak_perhutani
|--------------------------------------------------------------------------
| Stores forest parcel (petak perhutani) spatial records.
| Primary key is bigint unsigned (id).
| Contains administrative hierarchy (desa, kecamatan, kabupaten, provinsi) as text fields.
| Contains spatial geometry column for polygon data.
| atribut stored as longtext (JSON) for flexible attribute data.
| No timestamps on this table.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petak_perhutani', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->string('kode_toponimi', 30)->nullable();
            $table->string('petak', 20)->nullable();
            $table->string('anak_petak', 30)->nullable();
            $table->string('bagian_hutan', 100)->nullable();
            $table->string('kelompok_hutan', 100)->nullable();
            $table->string('rph', 100)->nullable();
            $table->string('bkph', 100)->nullable();
            $table->string('kph', 100)->nullable();
            $table->string('desa', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kabupaten', 100)->nullable();
            $table->string('provinsi', 100)->nullable();
            $table->string('fungsi', 30)->nullable();
            $table->string('klas_hutan', 50)->nullable();
            $table->decimal('luas_baku', 12, 2)->nullable();
            $table->string('jenis_tanaman', 100)->nullable();
            $table->integer('tahun_tanam')->nullable();
            $table->polygon('geometry');
            $table->longText('atribut')->nullable();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petak_perhutani');
    }
};
