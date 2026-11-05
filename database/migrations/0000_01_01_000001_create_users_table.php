<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: users
|--------------------------------------------------------------------------
| Application users with role-based access.
| id_cdk references cdk.id (no FK constraint - documentation only).
| id_kelompok references kelompok.id (no FK constraint - documentation only).
| Only created_at timestamp; updated_at is intentionally excluded (CONST UPDATED_AT = null in model).
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('nama', 100);
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->enum('role', ['admin', 'admin_cdk', 'pendamping', 'kelompok']);
            $table->unsignedInteger('id_cdk')->nullable(); // ref: cdk.id
            $table->integer('id_kelompok')->nullable(); // ref: kelompok.id
            $table->timestamp('created_at')->useCurrent();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
