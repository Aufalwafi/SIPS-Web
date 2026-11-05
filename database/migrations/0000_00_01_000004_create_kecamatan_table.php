<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: kecamatan
|--------------------------------------------------------------------------
| Stores kecamatan (district) records within a kabupaten.
| kabupaten_id references kabupaten.id (no FK constraint - documentation only).
| No timestamps on this reference table.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kecamatan', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('kabupaten_id'); // ref: kabupaten.id
            $table->string('kode', 10);
            $table->string('kode_wilayah', 20)->nullable();
            $table->string('nama', 150);

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kecamatan');
    }
};
