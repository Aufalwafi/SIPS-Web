<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DokumentasiKegiatan;
use App\Services\ExifGpsService;
use App\Services\GoogleDriveService;

class InjectExifGpsCommand extends Command
{
    protected $signature = 'geotag:inject-exif';
    protected $description = 'Menyisipkan metadata EXIF GPS langsung ke file fisik foto dokumentasi';

    public function handle(GoogleDriveService $driveService)
    {
        $photos = DokumentasiKegiatan::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $this->info("Menemukan {$photos->count()} foto berkoordinat.");
        $updated = 0;

        foreach ($photos as $doc) {
            $filePath = $driveService->findPhysicalPath($doc);
            if (!$filePath || !file_exists($filePath)) {
                $this->warn("File tidak ditemukan: {$doc->nama_file}");
                continue;
            }

            $raw = file_get_contents($filePath);
            $tagged = ExifGpsService::injectGps(
                $raw,
                (float)$doc->latitude,
                (float)$doc->longitude,
                $doc->altitude ? (float)$doc->altitude : null,
                $doc->tanggal_foto ? \Carbon\Carbon::parse($doc->tanggal_foto) : now()
            );

            file_put_contents($filePath, $tagged);
            $this->info("Berhasil disisipkan EXIF GPS: {$doc->nama_file}");
            $updated++;
        }

        $this->info("Selesai! {$updated} file foto berhasil diperbarui metadata EXIF-nya.");
        return 0;
    }
}
