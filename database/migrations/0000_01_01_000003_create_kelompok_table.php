<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: kelompok
|--------------------------------------------------------------------------
| Stores kelompok (farmer group) records.
| id_cdk references cdk.id (no FK constraint - documentation only).
| desa_id references desa.id (no FK constraint - documentation only).
| id_pendamping references users.id (no FK constraint - documentation only).
| Contains geometry fields for spatial data.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelompok', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('id_cdk')->nullable(); // ref: cdk.id
            $table->unsignedInteger('desa_id')->nullable(); // ref: desa.id
            $table->string('nama_kelompok', 150)->nullable();
            $table->string('ketua', 100)->nullable();
            $table->string('desa', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kabupaten', 100)->nullable();
            $table->string('skema_ps', 50)->nullable();
            $table->decimal('luas_lahan', 10, 2)->nullable();
            $table->string('no_sk', 100)->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->integer('id_pendamping'); // ref: users.id
            $table->timestamp('created_at')->useCurrent();
            $table->string('sk_ps', 255)->nullable();
            $table->string('sk_kelompok', 255)->nullable();
            $table->longText('polygon_kelompok')->nullable();
            $table->polygon('geometry')->nullable();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelompok');
    }
};
