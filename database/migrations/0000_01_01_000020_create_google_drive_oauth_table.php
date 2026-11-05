<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: google_drive_oauth
|--------------------------------------------------------------------------
| Stores Google Drive OAuth2 credentials for API integration.
| Contains access and refresh tokens for each connected account.
| Tokens are stored as text for potentially long JWT values.
| Both created_at and updated_at are nullable timestamps.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_drive_oauth', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('email', 255)->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_drive_oauth');
    }
};
