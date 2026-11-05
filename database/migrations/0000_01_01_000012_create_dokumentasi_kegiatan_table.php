<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Table: dokumentasi_kegiatan
|--------------------------------------------------------------------------
| Stores photo/media documentation for kegiatan activities.
| id_kegiatan references kegiatan.id (no FK constraint - documentation only).
| id_user references users.id (no FK constraint - documentation only).
| foto_uuid is unique and used for file identification.
| Supports EXIF geotag data (latitude, longitude, altitude, accuracy).
| Includes Google Drive sync fields for cloud backup.
| This is the most complex table in the schema.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumentasi_kegiatan', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('id_kegiatan'); // ref: kegiatan.id
            $table->integer('id_user'); // ref: users.id
            $table->string('foto_uuid', 64)->nullable()->unique();
            $table->string('nama_file', 255);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('altitude', 8, 2)->nullable();
            $table->decimal('akurasi', 6, 2)->nullable();
            $table->dateTime('tanggal_foto')->nullable();
            $table->text('alamat')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('judul', 200)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->string('file_type', 100)->nullable();
            $table->text('metadata')->nullable();
            $table->enum('status_geotag', ['EXIF', 'KAMERA', 'TIDAK_ADA'])->nullable();
            $table->string('google_drive_status', 20);
            $table->string('google_drive_file_id', 100)->nullable();
            $table->text('google_drive_error')->nullable();
            $table->dateTime('google_drive_synced_at')->nullable();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumentasi_kegiatan');
    }
};
