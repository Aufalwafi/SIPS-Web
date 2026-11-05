<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: user_logs
|--------------------------------------------------------------------------
| Tracks user login/logout activity and session state.
| user_id references users.id (no FK constraint - documentation only).
| Logs IP address for audit purposes.
| No timestamps on this table.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_logs', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('user_id')->nullable(); // ref: users.id
            $table->dateTime('login_time')->nullable();
            $table->dateTime('logout_time')->nullable();
            $table->dateTime('last_activity')->nullable();
            $table->string('ip_address', 45)->nullable();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_logs');
    }
};
