<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cdk;
use App\Models\DokumentasiKegiatan;
use App\Models\Kegiatan;
use App\Models\Kelompok;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;

class GoogleDriveSyncController extends Controller
{
    /**
     * Helper query filter terpusat untuk index dan getItems
     */
    protected function buildFilteredQuery(Request $request, bool $includeStatus = true)
    {
        $query = DokumentasiKegiatan::with(['kegiatan.kelompok.cdk']);

        $search = trim((string) $request->query('q', ''));
        $selectedStatus = $request->query('status');
        $selectedCdk = $request->query('id_cdk');
        $selectedKelompok = $request->query('id_kelompok');
        $selectedId = $request->query('id_kegiatan');

        // 1. Filter Pencarian Teks (Nama File / Keterangan / Judul / Nama Kegiatan / Lokasi / Kelompok / Desa / Kecamatan / Kabupaten / CDK)
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_file', 'like', "%{$search}%")
                  ->orWhere('file_path', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('judul', 'like', "%{$search}%")
                  ->orWhereHas('kegiatan', function ($kq) use ($search) {
                      $kq->where('nama_kegiatan', 'like', "%{$search}%")
                         ->orWhere('lokasi', 'like', "%{$search}%")
                         ->orWhere('keterangan', 'like', "%{$search}%")
                         ->orWhereHas('kelompok', function ($klpq) use ($search) {
                             $klpq->where('nama_kelompok', 'like', "%{$search}%")
                                  ->orWhere('desa', 'like', "%{$search}%")
                                  ->orWhere('kecamatan', 'like', "%{$search}%")
                                  ->orWhere('kabupaten', 'like', "%{$search}%")
                                  ->orWhere('ketua', 'like', "%{$search}%")
                                  ->orWhereHas('cdk', function ($cdkq) use ($search) {
                                      $cdkq->where('nama', 'like', "%{$search}%")
                                           ->orWhere('kode', 'like', "%{$search}%");
                                  });
                         });
                  });
            });
        }

        // 2. Filter Status Sinkronisasi
        if ($includeStatus && $selectedStatus && $selectedStatus !== 'all') {
            if ($selectedStatus === 'synced' || $selectedStatus === 'success') {
                $query->whereIn('google_drive_status', ['success', 'synced']);
            } elseif ($selectedStatus === 'pending') {
                $query->where(function ($q) {
                    $q->whereNull('google_drive_status')
                      ->orWhereIn('google_drive_status', ['', 'pending', 'menunggu']);
                });
            } elseif ($selectedStatus === 'failed' || $selectedStatus === 'error' || $selectedStatus === 'gagal') {
                $query->whereIn('google_drive_status', ['error', 'failed', 'gagal']);
            }
        }

        // 3. Filter CDK Wilayah
        if ($selectedCdk && $selectedCdk !== 'all') {
            $query->whereHas('kegiatan.kelompok', function ($q) use ($selectedCdk) {
                if (is_numeric($selectedCdk)) {
                    $q->where('id_cdk', (int) $selectedCdk);
                } else {
                    $q->whereHas('cdk', function ($cq) use ($selectedCdk) {
                        $cq->where('nama', 'like', "%{$selectedCdk}%");
                    });
                }
            });
        }

        // 4. Filter Kelompok
        if ($selectedKelompok && $selectedKelompok !== 'all') {
            $query->whereHas('kegiatan', function ($q) use ($selectedKelompok) {
                if (is_numeric($selectedKelompok)) {
                    $q->where('id_kelompok', (int) $selectedKelompok);
                } else {
                    $q->whereHas('kelompok', function ($klpq) use ($selectedKelompok) {
                        $klpq->where('nama_kelompok', 'like', "%{$selectedKelompok}%");
                    });
                }
            });
        }

        // 5. Filter Kegiatan
        if ($selectedId && $selectedId !== 'all') {
            if (is_numeric($selectedId)) {
                $query->where('id_kegiatan', (int) $selectedId);
            } else {
                $query->whereHas('kegiatan', function ($kq) use ($selectedId) {
                    $kq->where('nama_kegiatan', 'like', "%{$selectedId}%");
                });
            }
        }

        return $query;
    }

    /**
     * Tampilkan halaman utama sinkronisasi Google Drive dengan filter terpadu
     */
    public function index(Request $request)
    {
        $cdkList = Cdk::orderBy('nama')->get();
        $kelompokList = Kelompok::with('cdk')
            ->select('id', 'id_cdk', 'nama_kelompok')
            ->orderBy('nama_kelompok')
            ->get();
        $kegiatanList = Kegiatan::with(['kelompok.cdk'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        $search = trim((string) $request->query('q', ''));
        $selectedStatus = $request->query('status');
        $selectedCdk = $request->query('id_cdk');
        $selectedKelompok = $request->query('id_kelompok');
        $selectedId = $request->query('id_kegiatan');

        // Query utama dengan filter status
        $query = $this->buildFilteredQuery($request, true);

        // Query dasar tanpa filter status agar angka statistik konteks tetap akurat
        $baseQuery = $this->buildFilteredQuery($request, false);

        $stats = [
            'total'   => (clone $baseQuery)->count(),
            'synced'  => (clone $baseQuery)->whereIn('google_drive_status', ['success', 'synced'])->count(),
            'failed'  => (clone $baseQuery)->whereIn('google_drive_status', ['error', 'failed', 'gagal'])->count(),
        ];
        $stats['pending'] = max(0, $stats['total'] - $stats['synced'] - $stats['failed']);

        $dokumentasiList = (clone $query)->orderBy('id', 'desc')->get();

        $driveService = app(GoogleDriveService::class);
        $isDriveConnected = $driveService->isConnected();
        $driveFolderUrl = $driveService->getFolderUrl();

        return view('admin.google-drive-sync', compact(
            'cdkList',
            'kelompokList',
            'kegiatanList',
            'search',
            'selectedStatus',
            'selectedCdk',
            'selectedKelompok',
            'selectedId',
            'stats',
            'dokumentasiList',
            'isDriveConnected',
            'driveFolderUrl'
        ));
    }

    /**
     * Dapatkan daftar JSON foto untuk diproses satu per satu
     */
    public function getItems(Request $request)
    {
        $query = $this->buildFilteredQuery($request);

        $docs = $query->orderBy('id', 'asc')->get();

        $items = $docs->map(function ($doc) {
            $kelompok = $doc->kegiatan ? $doc->kegiatan->kelompok : null;
            $cdkName = 'CDK Wilayah';
            if ($kelompok && $kelompok->cdk && !empty($kelompok->cdk->nama)) {
                $cdkName = $kelompok->cdk->nama;
            }

            return [
                'id'                   => $doc->id,
                'id_kegiatan'          => $doc->id_kegiatan,
                'nama_file'            => $doc->nama_file ?: basename($doc->file_path ?? 'foto.jpg'),
                'google_drive_status'  => $doc->google_drive_status,
                'google_drive_file_id' => $doc->google_drive_file_id,
                'cdk'                  => $cdkName,
                'kelompok'             => $kelompok ? $kelompok->nama_kelompok : 'Kelompok',
                'kegiatan'             => $doc->kegiatan ? $doc->kegiatan->nama_kegiatan : 'Kegiatan',
            ];
        });

        return response()->json([
            'success' => true,
            'total'   => $items->count(),
            'items'   => $items,
        ]);
    }

    /**
     * Proses verifikasi & sinkronisasi 1 item foto
     */
    public function processItem(Request $request, GoogleDriveService $driveService)
    {
        $id = $request->input('id');
        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter ID tidak valid.',
            ], 400);
        }

        $doc = DokumentasiKegiatan::with(['kegiatan.kelompok.cdk'])->find($id);
        if (!$doc) {
            return response()->json([
                'success' => false,
                'message' => 'Data dokumentasi tidak ditemukan.',
            ], 404);
        }

        $result = $driveService->syncDokumentasiDetailed($doc, forceVerify: true);

        return response()->json([
            'success'  => $result['success'],
            'status'   => $result['status'], // 'skipped', 'uploaded', 'failed'
            'message'  => $result['message'],
            'file_id'  => $result['file_id'] ?? null,
            'doc_id'   => $doc->id,
            'filename' => $doc->nama_file ?: basename($doc->file_path ?? 'foto.jpg'),
        ]);
    }
}
