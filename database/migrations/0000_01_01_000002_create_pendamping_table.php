<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: pendamping
|--------------------------------------------------------------------------
| Stores pendamping (facilitator/mentor) records.
| id_cdk references cdk.id (no FK constraint - documentation only).
| Primary key is id_pendamping (not the default 'id').
| Has both created_at and updated_at timestamps.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendamping', function (Blueprint $table) {
            $table->integer('id_pendamping')->autoIncrement();
            $table->unsignedInteger('id_cdk')->nullable(); // ref: cdk.id
            $table->string('nama_pendamping', 150)->nullable();
            $table->string('nip', 30)->nullable();
            $table->string('username', 50)->nullable()->unique();
            $table->string('password', 255)->nullable();
            $table->string('no_hp', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('alamat')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendamping');
    }
};
