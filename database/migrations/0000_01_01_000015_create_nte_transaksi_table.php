<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: nte_transaksi
|--------------------------------------------------------------------------
| Stores NTE transaction records (monthly sales/volume).
| id_nte references nte_kelompok.id (no FK constraint - documentation only).
| Tracks monthly volume, unit price, and total NTE economy value.
| No timestamps on this table.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nte_transaksi', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('id_nte'); // ref: nte_kelompok.id
            $table->integer('bulan')->nullable();
            $table->decimal('volume', 12, 2)->nullable();
            $table->string('satuan', 50)->nullable();
            $table->decimal('harga_satuan', 15, 2)->nullable();
            $table->decimal('total_nekon', 15, 2)->nullable();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nte_transaksi');
    }
};
