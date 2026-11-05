<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: cdk_kabupaten
|--------------------------------------------------------------------------
| Junction table linking CDK records to kabupaten.
| cdk_id references cdk.id (no FK constraint - documentation only).
| kabupaten_id references kabupaten.id (no FK constraint - documentation only).
| No timestamps on this junction table.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cdk_kabupaten', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('cdk_id'); // ref: cdk.id
            $table->unsignedInteger('kabupaten_id')->unique(); // ref: kabupaten.id

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cdk_kabupaten');
    }
};
