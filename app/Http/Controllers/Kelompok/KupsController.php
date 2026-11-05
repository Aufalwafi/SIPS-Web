<?php

namespace App\Http\Controllers\Kelompok;

use App\Models\Kups;
use Illuminate\Http\Request;

class KupsController extends BaseKelompokController
{
    public function kups()
    {
        $kelompok = $this->authorizeKelompok();
        $kupsList = Kups::where('id_kelompok', $kelompok->id)->orderBy('nama_kups')->get();
        return view('kelompok.kelompok_kups', compact('kelompok', 'kupsList') + $this->dataNavigasi());
    }

    public function tambahKups()
    {
        $kelompok = $this->authorizeKelompok();
        return view('kelompok.kelompok_tambah_kups', compact('kelompok') + $this->dataNavigasi());
    }

    public function simpanKups(Request $request)
    {
        $kelompok = $this->authorizeKelompok();

        $data = $request->validate([
            'nama_kups'       => ['required', 'string', 'max:200'],
            'ketua_kups'      => ['nullable', 'string', 'max:200'],
            'jumlah_anggota'  => ['nullable', 'integer'],
            'kelas_kups'      => ['nullable', 'in:Biru,Perak,Emas,Platinum'],
            'tahun_bentuk'    => ['nullable', 'digits:4'],
            'jenis_usaha'     => ['nullable', 'string', 'max:200'],
            'produk'          => ['nullable', 'string', 'max:200'],
            'produksi_tahun'  => ['nullable', 'integer'],
            'satuan_produksi' => ['nullable', 'string', 'max:50'],
            'keterangan'      => ['nullable', 'string'],
        ]);

        Kups::create(array_merge($data, ['id_kelompok' => $kelompok->id]));

        return redirect()->route('kelompok.kups')->with('success', 'KUPS berhasil ditambahkan.');
    }

    public function editKups($id)
    {
        $kelompok = $this->authorizeKelompok();
        $kups = $this->checkOwnership($kelompok->id, $id, 'kups');
        if (!$kups) {
            return redirect()->route('kelompok.kups')->with('error', 'KUPS tidak ditemukan.');
        }
        return view('kelompok.kelompok_edit_kups', compact('kelompok', 'kups') + $this->dataNavigasi());
    }

    public function updateKups(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompok();
        $kups = $this->checkOwnership($kelompok->id, $id, 'kups');
        if (!$kups) {
            return redirect()->route('kelompok.kups')->with('error', 'KUPS tidak ditemukan.');
        }

        $data = $request->validate([
            'nama_kups'       => ['required', 'string', 'max:200'],
            'ketua_kups'      => ['nullable', 'string', 'max:200'],
            'jumlah_anggota'  => ['nullable', 'integer'],
            'kelas_kups'      => ['nullable', 'in:Biru,Perak,Emas,Platinum'],
            'tahun_bentuk'    => ['nullable', 'digits:4'],
            'jenis_usaha'     => ['nullable', 'string', 'max:200'],
            'produk'          => ['nullable', 'string', 'max:200'],
            'produksi_tahun'  => ['nullable', 'integer'],
            'satuan_produksi' => ['nullable', 'string', 'max:50'],
            'keterangan'      => ['nullable', 'string'],
        ]);

        Kups::where('id', $id)->update($data);

        return redirect()->route('kelompok.kups')->with('success', 'KUPS berhasil diperbarui.');
    }

    public function hapusKups($id)
    {
        $kelompok = $this->authorizeKelompok();
        $kups = $this->checkOwnership($kelompok->id, $id, 'kups');
        if (!$kups) {
            return redirect()->route('kelompok.kups')->with('error', 'KUPS tidak ditemukan.');
        }
        Kups::where('id', $id)->delete();

        return redirect()->route('kelompok.kups')->with('success', 'KUPS berhasil dihapus.');
    }
}
