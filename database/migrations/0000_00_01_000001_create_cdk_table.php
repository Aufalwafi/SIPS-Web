<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: cdk
|--------------------------------------------------------------------------
| Stores CDK (Citra Desa Kelompok) records.
| provinsi_id references provinsi.id (no FK constraint - documentation only).
| No timestamps on this table.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cdk', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('provinsi_id'); // ref: provinsi.id
            $table->string('kode', 20);
            $table->string('nama', 150);
            $table->text('keterangan')->nullable();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cdk');
    }
};
