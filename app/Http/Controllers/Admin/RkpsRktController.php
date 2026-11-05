<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RkpsRktController extends Controller
{
    /* ─── RKPS ───────────────────────────────────── */

    public function dataRkps()
    {
        $rkps = DB::table('rkps')
            ->leftJoin('kelompok', 'rkps.id_kelompok', '=', 'kelompok.id')
            ->orderByDesc('rkps.tahun_mulai')
            ->get(['rkps.*', 'kelompok.nama_kelompok']);

        return view('admin.data-rkps', compact('rkps'));
    }

    public function createRkps()
    {
        $daftarKelompok = DB::table('kelompok')
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);

        return view('admin.tambah-rkps', compact('daftarKelompok'));
    }

    public function storeRkps(Request $request)
    {
        $data = $request->validate([
            'id_kelompok' => ['required', 'integer', 'exists:kelompok,id'],
            'tahun_mulai' => ['required', 'integer', 'between:1900,2200'],
            'tahun_selesai' => ['required', 'integer', 'gte:tahun_mulai', 'between:1900,2200'],
            'no_penilaian' => ['nullable', 'string', 'max:100'],
            'tgl_penilaian' => ['nullable', 'date'],
            'no_pengesahan' => ['nullable', 'string', 'max:100'],
            'tgl_pengesahan' => ['nullable', 'date'],
            'file_rkps' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $filename = null;
        if ($request->hasFile('file_rkps')) {
            $filename = $this->storeRkpsFile($request->file('file_rkps'));
        }

        DB::table('rkps')->insert([
            'id_kelompok' => $data['id_kelompok'],
            'tahun_mulai' => $data['tahun_mulai'],
            'tahun_selesai' => $data['tahun_selesai'],
            'no_penilaian' => $data['no_penilaian'] ?? null,
            'tgl_penilaian' => $data['tgl_penilaian'] ?? null,
            'no_pengesahan' => $data['no_pengesahan'] ?? null,
            'tgl_pengesahan' => $data['tgl_pengesahan'] ?? null,
            'file_rkps' => $filename,
            'created_at' => now(),
        ]);

        return redirect()->route('admin.data-rkps')->with('success', 'Data RKPS berhasil ditambahkan.');
    }

    public function editRkps(int $id)
    {
        $rkps = DB::table('rkps')->where('id_rkps', $id)->first();
        abort_unless($rkps, 404);

        $daftarKelompok = DB::table('kelompok')
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);

        return view('admin.edit-rkps', compact('rkps', 'daftarKelompok'));
    }

    public function updateRkps(Request $request, int $id)
    {
        $rkps = DB::table('rkps')->where('id_rkps', $id)->first();
        abort_unless($rkps, 404);

        $data = $request->validate([
            'id_kelompok' => ['required', 'integer', 'exists:kelompok,id'],
            'tahun_mulai' => ['required', 'integer', 'between:1900,2200'],
            'tahun_selesai' => ['required', 'integer', 'gte:tahun_mulai', 'between:1900,2200'],
            'no_penilaian' => ['nullable', 'string', 'max:100'],
            'tgl_penilaian' => ['nullable', 'date'],
            'no_pengesahan' => ['nullable', 'string', 'max:100'],
            'tgl_pengesahan' => ['nullable', 'date'],
            'file_rkps' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $filename = $rkps->file_rkps;
        if ($request->hasFile('file_rkps')) {
            $filename = $this->storeRkpsFile($request->file('file_rkps'));
            $this->deleteRkpsFile($rkps->file_rkps);
        }

        DB::table('rkps')->where('id_rkps', $id)->update([
            'id_kelompok' => $data['id_kelompok'],
            'tahun_mulai' => $data['tahun_mulai'],
            'tahun_selesai' => $data['tahun_selesai'],
            'no_penilaian' => $data['no_penilaian'] ?? null,
            'tgl_penilaian' => $data['tgl_penilaian'] ?? null,
            'no_pengesahan' => $data['no_pengesahan'] ?? null,
            'tgl_pengesahan' => $data['tgl_pengesahan'] ?? null,
            'file_rkps' => $filename,
        ]);

        return redirect()->route('admin.data-rkps')->with('success', 'Data RKPS berhasil diperbarui.');
    }

    public function deleteRkps(int $id)
    {
        $rkps = DB::table('rkps')->where('id_rkps', $id)->first();
        abort_unless($rkps, 404);

        DB::table('rkps')->where('id_rkps', $id)->delete();
        $this->deleteRkpsFile($rkps->file_rkps);

        return redirect()->route('admin.data-rkps')->with('success', 'Data RKPS berhasil dihapus.');
    }

    private function storeRkpsFile($file): string
    {
        $directory = public_path('uploads/rkps');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        $filename = time() . '_' . Str::random(12) . '.' . strtolower($file->getClientOriginalExtension());
        $file->move($directory, $filename);
        return $filename;
    }

    private function deleteRkpsFile(?string $filename): void
    {
        if (! $filename || basename($filename) !== $filename) {
            return;
        }
        $path = public_path('uploads/rkps/' . $filename);
        if (is_file($path)) {
            unlink($path);
        }
    }

    /* ─── RKT ───────────────────────────────────── */

    public function dataRkt()
    {
        $rkt = DB::table('rkt')
            ->leftJoin('kelompok', 'rkt.id_kelompok', '=', 'kelompok.id')
            ->orderByDesc('rkt.tahun')
            ->get(['rkt.*', 'kelompok.nama_kelompok']);

        return view('admin.data-rkt', compact('rkt'));
    }

    public function createRkt()
    {
        $daftarKelompok = DB::table('kelompok')
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);
        $daftarRkps = DB::table('rkps')
            ->leftJoin('kelompok', 'rkps.id_kelompok', '=', 'kelompok.id')
            ->orderByDesc('rkps.tahun_mulai')
            ->get(['rkps.id_rkps', 'rkps.id_kelompok', 'rkps.tahun_mulai', 'rkps.tahun_selesai', 'kelompok.nama_kelompok']);

        return view('admin.tambah-rkt', compact('daftarKelompok', 'daftarRkps'));
    }

    public function storeRkt(Request $request)
    {
        $data = $request->validate([
            'id_kelompok' => ['required', 'integer', 'exists:kelompok,id'],
            'id_rkps' => ['nullable', 'integer', 'exists:rkps,id_rkps'],
            'tahun' => ['required', 'integer', 'between:1900,2200'],
            'no_penetapan' => ['nullable', 'string', 'max:100'],
            'tgl_penetapan' => ['nullable', 'date'],
            'file_rkt' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        if (!empty($data['id_rkps'])) {
            $rkpsBelongs = DB::table('rkps')->where('id_rkps', $data['id_rkps'])->where('id_kelompok', $data['id_kelompok'])->exists();
            if (! $rkpsBelongs) {
                return back()->withInput()->withErrors(['id_rkps' => 'RKPS yang dipilih bukan milik kelompok tersebut.']);
            }
        }

        $filename = $request->hasFile('file_rkt') ? $this->storeRktFile($request->file('file_rkt')) : null;

        DB::table('rkt')->insert([
            'id_kelompok' => $data['id_kelompok'],
            'id_rkps' => $data['id_rkps'] ?? null,
            'tahun' => $data['tahun'],
            'no_penetapan' => $data['no_penetapan'] ?? null,
            'tgl_penetapan' => $data['tgl_penetapan'] ?? null,
            'file_rkt' => $filename,
            'created_at' => now(),
        ]);

        return redirect()->route('admin.data-rkt')->with('success', 'Data RKT berhasil ditambahkan.');
    }

    public function editRkt(int $id)
    {
        $rkt = DB::table('rkt')->where('id_rkt', $id)->first();
        abort_unless($rkt, 404);
        $daftarKelompok = DB::table('kelompok')->orderBy('nama_kelompok')->get(['id', 'nama_kelompok']);
        $daftarRkps = DB::table('rkps')->leftJoin('kelompok', 'rkps.id_kelompok', '=', 'kelompok.id')->orderByDesc('rkps.tahun_mulai')->get(['rkps.id_rkps', 'rkps.id_kelompok', 'rkps.tahun_mulai', 'rkps.tahun_selesai', 'kelompok.nama_kelompok']);

        return view('admin.edit-rkt', compact('rkt', 'daftarKelompok', 'daftarRkps'));
    }

    public function updateRkt(Request $request, int $id)
    {
        $rkt = DB::table('rkt')->where('id_rkt', $id)->first();
        abort_unless($rkt, 404);
        $data = $request->validate([
            'id_kelompok' => ['required', 'integer', 'exists:kelompok,id'],
            'id_rkps' => ['nullable', 'integer', 'exists:rkps,id_rkps'],
            'tahun' => ['required', 'integer', 'between:1900,2200'],
            'no_penetapan' => ['nullable', 'string', 'max:100'],
            'tgl_penetapan' => ['nullable', 'date'],
            'file_rkt' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);
        if (!empty($data['id_rkps'])) {
            $rkpsBelongs = DB::table('rkps')->where('id_rkps', $data['id_rkps'])->where('id_kelompok', $data['id_kelompok'])->exists();
            if (! $rkpsBelongs) return back()->withInput()->withErrors(['id_rkps' => 'RKPS yang dipilih bukan milik kelompok tersebut.']);
        }
        $filename = $rkt->file_rkt;
        if ($request->hasFile('file_rkt')) {
            $filename = $this->storeRktFile($request->file('file_rkt'));
            $this->deleteRktFile($rkt->file_rkt);
        }
        DB::table('rkt')->where('id_rkt', $id)->update([
            'id_kelompok' => $data['id_kelompok'], 'id_rkps' => $data['id_rkps'] ?? null, 'tahun' => $data['tahun'],
            'no_penetapan' => $data['no_penetapan'] ?? null, 'tgl_penetapan' => $data['tgl_penetapan'] ?? null, 'file_rkt' => $filename,
        ]);
        return redirect()->route('admin.data-rkt')->with('success', 'Data RKT berhasil diperbarui.');
    }

    public function deleteRkt(int $id)
    {
        $rkt = DB::table('rkt')->where('id_rkt', $id)->first();
        abort_unless($rkt, 404);
        DB::table('rkt')->where('id_rkt', $id)->delete();
        $this->deleteRktFile($rkt->file_rkt);
        return redirect()->route('admin.data-rkt')->with('success', 'Data RKT berhasil dihapus.');
    }

    private function storeRktFile($file): string
    {
        $directory = public_path('uploads/rkt');
        if (! is_dir($directory)) mkdir($directory, 0755, true);
        $filename = time() . '_' . Str::random(12) . '.' . strtolower($file->getClientOriginalExtension());
        $file->move($directory, $filename);
        return $filename;
    }

    private function deleteRktFile(?string $filename): void
    {
        if (! $filename || basename($filename) !== $filename) return;
        $path = public_path('uploads/rkt/' . $filename);
        if (is_file($path)) unlink($path);
    }
}
