<?php

namespace App\Http\Controllers\Kelompok;

use App\Models\PengajuanRkps;
use App\Models\PengajuanRkt;
use App\Models\Rkps;
use App\Models\Rkt;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PengajuanController extends BaseKelompokController
{
    /* ─── Pengajuan RKPS ────────────────────────────────────── */

    public function pengajuanRkps()
    {
        $kelompok = $this->authorizeKelompok();
        $id = $kelompok->id;

        $rkpsList = Rkps::where('id_kelompok', $id)->orderByDesc('tahun_mulai')->get();
        $pengajuan = PengajuanRkps::where('id_kelompok', $id)->orderByDesc('tanggal_pengajuan')->get();
        $adaMenunggu = $pengajuan->contains('status', 'Menunggu');

        return view('kelompok.kelompok_pengajuan_rkps', compact('kelompok', 'rkpsList', 'pengajuan', 'adaMenunggu') + $this->dataNavigasi());
    }

    public function ajukanRkps()
    {
        $kelompok = $this->authorizeKelompok();
        return view('kelompok.kelompok_ajukan_rkps', compact('kelompok') + $this->dataNavigasi());
    }

    public function simpanAjukanRkps(Request $request)
    {
        $kelompok = $this->authorizeKelompok();

        $data = $request->validate([
            'tahun_mulai'   => ['required', 'integer', 'between:1900,2200'],
            'tahun_selesai' => ['required', 'integer', 'gte:tahun_mulai', 'between:1900,2200'],
            'file_rkps'     => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $file = $request->file('file_rkps');
        $filename = time() . '_' . Str::random(12) . '.pdf';
        $file->storeAs('rkps', $filename, 'public');

        PengajuanRkps::create([
            'id_kelompok'   => $kelompok->id,
            'tahun_mulai'   => $data['tahun_mulai'],
            'tahun_selesai' => $data['tahun_selesai'],
            'file_rkps'     => $filename,
            'status'        => 'Menunggu',
        ]);

        return redirect()->route('kelompok.pengajuan-rkps')
            ->with('success', 'Pengajuan RKPS berhasil dikirim dan menunggu verifikasi admin.');
    }

    public function detailPengajuanRkps($id)
    {
        $kelompok = $this->authorizeKelompok();
        $pengajuan = PengajuanRkps::where('id', $id)->where('id_kelompok', $kelompok->id)->first();
        if (!$pengajuan) {
            return redirect()->route('kelompok.pengajuan-rkps')->with('error', 'Pengajuan tidak ditemukan.');
        }
        return view('kelompok.kelompok_detail_pengajuan_rkps', compact('kelompok', 'pengajuan') + $this->dataNavigasi());
    }

    public function ajukanUlangRkps($id)
    {
        $kelompok = $this->authorizeKelompok();
        $pengajuan = PengajuanRkps::where('id', $id)->where('id_kelompok', $kelompok->id)->first();
        if (!$pengajuan || !in_array($pengajuan->status, ['Ditolak', 'Revisi'])) {
            return redirect()->route('kelompok.pengajuan-rkps')->with('error', 'Pengajuan tidak bisa diajukan ulang.');
        }
        return view('kelompok.kelompok_ajukan_ulang_rkps', compact('kelompok', 'pengajuan') + $this->dataNavigasi());
    }

    public function simpanAjukanUlangRkps(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompok();
        $pengajuan = PengajuanRkps::where('id', $id)->where('id_kelompok', $kelompok->id)->first();
        if (!$pengajuan || !in_array($pengajuan->status, ['Ditolak', 'Revisi'])) {
            return redirect()->route('kelompok.pengajuan-rkps')->with('error', 'Pengajuan tidak bisa diajukan ulang.');
        }

        $data = $request->validate([
            'tahun_mulai'   => ['required', 'integer', 'between:1900,2200'],
            'tahun_selesai' => ['required', 'integer', 'gte:tahun_mulai', 'between:1900,2200'],
            'file_rkps'     => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $file = $request->file('file_rkps');
        $filename = time() . '_' . Str::random(12) . '.pdf';
        $file->storeAs('rkps', $filename, 'public');

        PengajuanRkps::create([
            'id_kelompok'   => $kelompok->id,
            'tahun_mulai'   => $data['tahun_mulai'],
            'tahun_selesai' => $data['tahun_selesai'],
            'file_rkps'     => $filename,
            'status'        => 'Menunggu',
        ]);

        return redirect()->route('kelompok.pengajuan-rkps')
            ->with('success', 'Pengajuan RKPS berhasil diajukan ulang.');
    }

    /* ─── Pengajuan RKT ─────────────────────────────────────── */

    public function pengajuanRkt()
    {
        $kelompok = $this->authorizeKelompok();
        $id = $kelompok->id;

        $rktList = Rkt::where('id_kelompok', $id)->orderByDesc('tahun')->get();
        $pengajuan = PengajuanRkt::where('id_kelompok', $id)->orderByDesc('tanggal_pengajuan')->get();
        $adaMenunggu = $pengajuan->contains('status', 'Menunggu');

        return view('kelompok.kelompok_pengajuan_rkt', compact('kelompok', 'rktList', 'pengajuan', 'adaMenunggu') + $this->dataNavigasi());
    }

    public function ajukanRkt()
    {
        $kelompok = $this->authorizeKelompok();
        $daftarRkps = Rkps::where('id_kelompok', $kelompok->id)->orderByDesc('tahun_mulai')->get();
        return view('kelompok.kelompok_ajukan_rkt', compact('kelompok', 'daftarRkps') + $this->dataNavigasi());
    }

    public function simpanAjukanRkt(Request $request)
    {
        $kelompok = $this->authorizeKelompok();

        $data = $request->validate([
            'tahun'    => ['required', 'integer', 'between:1900,2200'],
            'file_rkt' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $file = $request->file('file_rkt');
        $filename = time() . '_' . Str::random(12) . '.pdf';
        $file->storeAs('rkt', $filename, 'public');

        PengajuanRkt::create([
            'id_kelompok' => $kelompok->id,
            'tahun'       => $data['tahun'],
            'file_rkt'    => $filename,
            'status'      => 'Menunggu',
        ]);

        return redirect()->route('kelompok.pengajuan-rkt')
            ->with('success', 'Pengajuan RKT berhasil dikirim dan menunggu verifikasi admin.');
    }

    public function detailPengajuanRkt($id)
    {
        $kelompok = $this->authorizeKelompok();
        $pengajuan = PengajuanRkt::where('id', $id)->where('id_kelompok', $kelompok->id)->first();
        if (!$pengajuan) {
            return redirect()->route('kelompok.pengajuan-rkt')->with('error', 'Pengajuan tidak ditemukan.');
        }
        return view('kelompok.kelompok_detail_pengajuan_rkt', compact('kelompok', 'pengajuan') + $this->dataNavigasi());
    }

    public function ajukanUlangRkt($id)
    {
        $kelompok = $this->authorizeKelompok();
        $pengajuan = PengajuanRkt::where('id', $id)->where('id_kelompok', $kelompok->id)->first();
        if (!$pengajuan || !in_array($pengajuan->status, ['Ditolak', 'Revisi'])) {
            return redirect()->route('kelompok.pengajuan-rkt')->with('error', 'Pengajuan tidak bisa diajukan ulang.');
        }
        $daftarRkps = Rkps::where('id_kelompok', $kelompok->id)->orderByDesc('tahun_mulai')->get();
        return view('kelompok.kelompok_ajukan_ulang_rkt', compact('kelompok', 'pengajuan', 'daftarRkps') + $this->dataNavigasi());
    }

    public function simpanAjukanUlangRkt(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompok();
        $pengajuan = PengajuanRkt::where('id', $id)->where('id_kelompok', $kelompok->id)->first();
        if (!$pengajuan || !in_array($pengajuan->status, ['Ditolak', 'Revisi'])) {
            return redirect()->route('kelompok.pengajuan-rkt')->with('error', 'Pengajuan tidak bisa diajukan ulang.');
        }

        $data = $request->validate([
            'tahun'    => ['required', 'integer', 'between:1900,2200'],
            'file_rkt' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $file = $request->file('file_rkt');
        $filename = time() . '_' . Str::random(12) . '.pdf';
        $file->storeAs('rkt', $filename, 'public');

        PengajuanRkt::create([
            'id_kelompok' => $kelompok->id,
            'tahun'       => $data['tahun'],
            'file_rkt'    => $filename,
            'status'      => 'Menunggu',
        ]);

        return redirect()->route('kelompok.pengajuan-rkt')
            ->with('success', 'Pengajuan RKT berhasil diajukan ulang.');
    }
}
