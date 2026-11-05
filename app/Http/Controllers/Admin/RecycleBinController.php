<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelompok;
use App\Models\AnggotaKelompok;
use App\Models\Kegiatan;
use App\Models\Kups;
use App\Models\PotensiKelompok;
use App\Models\PermasalahanKelompok;
use App\Models\Pendamping;
use Illuminate\Http\Request;

class RecycleBinController extends Controller
{
    private array $modelMap = [
        'kelompok'      => Kelompok::class,
        'anggota'       => AnggotaKelompok::class,
        'kegiatan'      => Kegiatan::class,
        'kups'          => Kups::class,
        'potensi'       => PotensiKelompok::class,
        'permasalahan'  => PermasalahanKelompok::class,
        'pendamping'    => Pendamping::class,
    ];

    public function index(Request $request)
    {
        $activeTab = $request->query('tab', 'kelompok');
        if (!array_key_exists($activeTab, $this->modelMap)) {
            $activeTab = 'kelompok';
        }

        // Auto-purge data yang sudah melebihi 30 hari di Recycle Bin
        $cutoffDate = now()->subDays(30);
        foreach ($this->modelMap as $modelClass) {
            $modelClass::onlyTrashed()->where('deleted_at', '<=', $cutoffDate)->forceDelete();
        }

        // Count all trashed items for badges
        $counts = [
            'kelompok'     => Kelompok::onlyTrashed()->count(),
            'anggota'      => AnggotaKelompok::onlyTrashed()->count(),
            'kegiatan'     => Kegiatan::onlyTrashed()->count(),
            'kups'         => Kups::onlyTrashed()->count(),
            'potensi'      => PotensiKelompok::onlyTrashed()->count(),
            'permasalahan' => PermasalahanKelompok::onlyTrashed()->count(),
            'pendamping'   => Pendamping::onlyTrashed()->count(),
        ];

        // Fetch data for the active tab
        $items = collect();
        switch ($activeTab) {
            case 'kelompok':
                $items = Kelompok::onlyTrashed()->with('cdk')->orderByDesc('deleted_at')->get();
                break;
            case 'anggota':
                $items = AnggotaKelompok::onlyTrashed()->with('kelompok')->orderByDesc('deleted_at')->get();
                break;
            case 'kegiatan':
                $items = Kegiatan::onlyTrashed()->with('kelompok')->orderByDesc('deleted_at')->get();
                break;
            case 'kups':
                $items = Kups::onlyTrashed()->with('kelompok')->orderByDesc('deleted_at')->get();
                break;
            case 'potensi':
                $items = PotensiKelompok::onlyTrashed()->with('kelompok')->orderByDesc('deleted_at')->get();
                break;
            case 'permasalahan':
                $items = PermasalahanKelompok::onlyTrashed()->with('kelompok')->orderByDesc('deleted_at')->get();
                break;
            case 'pendamping':
                $items = Pendamping::onlyTrashed()->with('cdk')->orderByDesc('deleted_at')->get();
                break;
        }

        return view('admin.recycle_bin', compact('items', 'activeTab', 'counts'));
    }

    public function restore(string $type, int $id)
    {
        abort_unless(array_key_exists($type, $this->modelMap), 404, 'Tipe data tidak valid');

        $modelClass = $this->modelMap[$type];
        $item = $modelClass::onlyTrashed()->find($id);
        abort_unless($item, 404, 'Data yang dihapus tidak ditemukan');

        $item->restore();

        return redirect()->route('admin.recycle-bin', ['tab' => $type])
            ->with('success', 'Data berhasil dipulihkan (restore) dan kembali aktif.');
    }

    public function forceDelete(string $type, int $id)
    {
        abort_unless(array_key_exists($type, $this->modelMap), 404, 'Tipe data tidak valid');

        $modelClass = $this->modelMap[$type];
        $item = $modelClass::onlyTrashed()->find($id);
        abort_unless($item, 404, 'Data yang dihapus tidak ditemukan');

        $item->forceDelete();

        return redirect()->route('admin.recycle-bin', ['tab' => $type])
            ->with('success', 'Data telah berhasil dihapus secara permanen dari database.');
    }
}
