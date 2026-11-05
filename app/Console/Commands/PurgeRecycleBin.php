<?php

namespace App\Console\Commands;

use App\Models\Kelompok;
use App\Models\AnggotaKelompok;
use App\Models\Kegiatan;
use App\Models\Kups;
use App\Models\PotensiKelompok;
use App\Models\PermasalahanKelompok;
use App\Models\Pendamping;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PurgeRecycleBin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recycle-bin:purge {--days=30 : Batas hari data di recycle bin sebelum dihapus permanen}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus permanen data di Recycle Bin yang sudah berusia lebih dari jumlah hari yang ditentukan (default 30 hari)';

    private array $models = [
        'Kelompok'             => Kelompok::class,
        'Anggota Kelompok'     => AnggotaKelompok::class,
        'Kegiatan'             => Kegiatan::class,
        'KUPS'                 => Kups::class,
        'Potensi Kelompok'     => PotensiKelompok::class,
        'Permasalahan Kelompok'=> PermasalahanKelompok::class,
        'Pendamping'           => Pendamping::class,
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        if ($days < 1) {
            $days = 30;
        }

        $cutoffDate = now()->subDays($days);
        $totalPurged = 0;

        $this->info("Memulai pembersihan data Recycle Bin yang terhapus sebelum: " . $cutoffDate->format('Y-m-d H:i:s'));

        foreach ($this->models as $label => $modelClass) {
            $query = $modelClass::onlyTrashed()->where('deleted_at', '<=', $cutoffDate);
            $count = $query->count();

            if ($count > 0) {
                // Force delete permanently
                $query->forceDelete();
                $totalPurged += $count;
                $this->line(" - {$label}: {$count} data dihapus permanen.");
            }
        }

        if ($totalPurged > 0) {
            $message = "Recycle Bin Auto-Purge: Total {$totalPurged} data berusia > {$days} hari berhasil dihapus permanen.";
            $this->info($message);
            Log::info($message);
        } else {
            $this->info("Tidak ada data lama yang melebihi {$days} hari di Recycle Bin.");
        }

        return self::SUCCESS;
    }
}
