<?php

namespace App\Console\Commands;

use App\Models\DokumentasiKegiatan;
use App\Services\GoogleDriveService;
use Illuminate\Console\Command;

class SyncGoogleDrivePhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drive:sync 
                            {--all : Sinkronisasi semua foto tanpa terkecuali}
                            {--limit=50 : Batas jumlah foto yang diproses dalam sekali jalan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi foto dokumentasi kegiatan ke Google Drive';

    /**
     * Execute the console command.
     */
    public function handle(GoogleDriveService $driveService)
    {
        $this->info('=== SINKRONISASI FOTO KE GOOGLE DRIVE ===');
        $folderUrl = $driveService->getFolderUrl();
        $this->line("Target Folder: {$folderUrl}");

        $query = DokumentasiKegiatan::query();
        if (!$this->option('all')) {
            $query->where(function ($q) {
                $q->whereNull('google_drive_file_id')
                  ->orWhere('google_drive_status', '!=', 'success');
            });
        }

        $limit = (int) $this->option('limit');
        $docs = $query->limit($limit)->get();

        if ($docs->isEmpty()) {
            $this->info('Semua foto sudah tersinkron ke Google Drive.');
            return Command::SUCCESS;
        }

        $this->info("Menemukan {$docs->count()} foto yang perlu disinkronkan...");
        $bar = $this->output->createProgressBar($docs->count());
        $bar->start();

        $successCount = 0;
        $failCount = 0;

        foreach ($docs as $doc) {
            $success = $driveService->syncDokumentasi($doc);
            if ($success) {
                $successCount++;
            } else {
                $failCount++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Hasil Sinkronisasi:");
        $this->line("- Berhasil : {$successCount}");
        $this->line("- Gagal    : {$failCount}");

        if ($failCount > 0) {
            $this->warn('Beberapa foto gagal disinkronkan. Pastikan koneksi internet aktif dan kredensial Google diatur.');
        }

        return Command::SUCCESS;
    }
}
