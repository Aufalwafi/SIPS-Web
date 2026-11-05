<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: desa
|--------------------------------------------------------------------------
| Stores desa (village) records within a kecamatan.
| kecamatan_id references kecamatan.id (no FK constraint - documentation only).
| Contains historical village codes (kode_2013, kode_2012).
| No timestamps on this reference table.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('desa', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('kecamatan_id'); // ref: kecamatan.id
            $table->string('kode', 10);
            $table->bigInteger('kode_2013')->nullable();
            $table->bigInteger('kode_2012')->nullable();
            $table->string('nama', 150);

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desa');
    }
};
