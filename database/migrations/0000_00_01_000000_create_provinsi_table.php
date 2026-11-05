<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: provinsi
|--------------------------------------------------------------------------
| Stores Indonesian provinces (provinsi).
| Each province has a code (kode) and a name (nama).
| No timestamps on this reference table.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinsi', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('kode', 10)->unique();
            $table->string('nama', 100);

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provinsi');
    }
};
