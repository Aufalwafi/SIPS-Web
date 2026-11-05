<?php

namespace App\Http\Controllers\Kelompok;

use App\Models\AnggotaKelompok;
use Illuminate\Http\Request;

class AnggotaController extends BaseKelompokController
{
    public function dataAnggota()
    {
        $kelompok = $this->authorizeKelompok();
        $anggota = AnggotaKelompok::where('id_kelompok', $kelompok->id)->orderBy('nama_anggota')->get();
        return view('kelompok.kelompok_anggota', compact('anggota', 'kelompok') + $this->dataNavigasi());
    }

    public function tambahAnggota()
    {
        $kelompok = $this->authorizeKelompok();
        return view('kelompok.kelompok_tambah_anggota', compact('kelompok') + $this->dataNavigasi());
    }

    public function simpanAnggota(Request $request)
    {
        $kelompok = $this->authorizeKelompok();

        $data = $request->validate([
            'nama_anggota'    => ['required', 'string', 'max:100'],
            'nik'             => ['nullable', 'string', 'max:20'],
            'jenis_kelamin'   => ['nullable', 'in:L,P'],
            'alamat'          => ['nullable', 'string'],
            'luas_garapan'    => ['nullable', 'numeric'],
            'komoditas_utama' => ['nullable', 'string', 'max:100'],
        ]);

        AnggotaKelompok::create(array_merge($data, [
            'id_kelompok' => $kelompok->id,
            'polygon'     => null,
        ]));

        return redirect()->route('kelompok.anggota')->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function editAnggota($id)
    {
        $kelompok = $this->authorizeKelompok();
        $anggota = $this->checkOwnership($kelompok->id, $id, 'anggota_kelompok');
        if (!$anggota) {
            return redirect()->route('kelompok.anggota')->with('error', 'Anggota tidak ditemukan.');
        }
        return view('kelompok.kelompok_edit_anggota', compact('anggota', 'kelompok') + $this->dataNavigasi());
    }

    public function updateAnggota(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompok();
        $anggota = $this->checkOwnership($kelompok->id, $id, 'anggota_kelompok');
        if (!$anggota) {
            return redirect()->route('kelompok.anggota')->with('error', 'Anggota tidak ditemukan.');
        }

        $data = $request->validate([
            'nama_anggota'    => ['required', 'string', 'max:100'],
            'nik'             => ['nullable', 'string', 'max:20'],
            'jenis_kelamin'   => ['nullable', 'in:L,P'],
            'alamat'          => ['nullable', 'string'],
            'luas_garapan'    => ['nullable', 'numeric'],
            'komoditas_utama' => ['nullable', 'string', 'max:100'],
        ]);

        AnggotaKelompok::where('id', $id)->update($data);

        return redirect()->route('kelompok.anggota')->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function hapusAnggota($id)
    {
        $kelompok = $this->authorizeKelompok();
        $anggota = $this->checkOwnership($kelompok->id, $id, 'anggota_kelompok');
        if (!$anggota) {
            return redirect()->route('kelompok.anggota')->with('error', 'Anggota tidak ditemukan.');
        }
        AnggotaKelompok::where('id', $id)->delete();

        return redirect()->route('kelompok.anggota')->with('success', 'Anggota berhasil dihapus.');
    }
}
