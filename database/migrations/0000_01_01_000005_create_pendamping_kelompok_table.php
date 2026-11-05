<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: pendamping_kelompok
|--------------------------------------------------------------------------
| Junction table linking users (pendamping role) to kelompok.
| id_user references users.id (no FK constraint - documentation only).
| id_kelompok references kelompok.id (no FK constraint - documentation only).
| No timestamps on this junction table.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendamping_kelompok', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('id_user'); // ref: users.id
            $table->integer('id_kelompok'); // ref: kelompok.id

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendamping_kelompok');
    }
};
