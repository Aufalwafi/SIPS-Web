<?php

namespace App\Http\Controllers\Kelompok;

use App\Models\PotensiKelompok;
use App\Models\PermasalahanKelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PotensiPermasalahanController extends BaseKelompokController
{
    /* ─── Potensi CRUD ──────────────────────────────────────── */

    public function potensi()
    {
        $kelompok = $this->authorizeKelompok();
        $potensiList = PotensiKelompok::where('id_kelompok', $kelompok->id)
            ->orderBy('jenis_potensi', 'asc')
            ->orderBy('jenis_detail', 'asc')
            ->get();
        return view('kelompok.kelompok_potensi', compact('kelompok', 'potensiList') + $this->dataNavigasi());
    }

    public function tambahPotensi()
    {
        $kelompok = $this->authorizeKelompok();
        return view('kelompok.kelompok_tambah_potensi', compact('kelompok') + $this->dataNavigasi());
    }

    public function simpanPotensi(Request $request)
    {
        $kelompok = $this->authorizeKelompok();
        $data = $request->validate([
            'jenis_potensi' => ['required', 'in:Kayu,Non Kayu,Jasa Lingkungan'],
            'jenis_detail'  => ['required', 'string', 'max:150'],
            'tahun_tanam'   => ['nullable', 'digits:4'],
            'luasan'        => ['nullable', 'numeric'],
            'satuan'        => ['nullable', 'string', 'max:30'],
            'kondisi'       => ['nullable', 'string', 'max:100'],
            'lokasi'        => ['nullable', 'string', 'max:255'],
            'keterangan'    => ['nullable', 'string'],
            'foto'          => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('potensi', 'public');
            $data['foto'] = basename($data['foto']);
        }

        PotensiKelompok::create(array_merge($data, ['id_kelompok' => $kelompok->id]));

        return redirect()->route('kelompok.potensi')->with('success', 'Potensi berhasil ditambahkan.');
    }

    public function editPotensi($id)
    {
        $kelompok = $this->authorizeKelompok();
        $potensi = PotensiKelompok::where('id', $id)->where('id_kelompok', $kelompok->id)->firstOrFail();
        return view('kelompok.kelompok_edit_potensi', compact('kelompok', 'potensi') + $this->dataNavigasi());
    }

    public function updatePotensi(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompok();
        $potensi = PotensiKelompok::where('id', $id)->where('id_kelompok', $kelompok->id)->firstOrFail();
        $data = $request->validate([
            'jenis_potensi' => ['required', 'in:Kayu,Non Kayu,Jasa Lingkungan'],
            'jenis_detail'  => ['required', 'string', 'max:150'],
            'tahun_tanam'   => ['nullable', 'digits:4'],
            'luasan'        => ['nullable', 'numeric'],
            'satuan'        => ['nullable', 'string', 'max:30'],
            'kondisi'       => ['nullable', 'string', 'max:100'],
            'lokasi'        => ['nullable', 'string', 'max:255'],
            'keterangan'    => ['nullable', 'string'],
            'foto'          => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('foto')) {
            if ($potensi->foto) {
                Storage::disk('public')->delete('potensi/' . $potensi->foto);
            }
            $data['foto'] = basename($request->file('foto')->store('potensi', 'public'));
        }

        $potensi->update($data);

        return redirect()->route('kelompok.potensi')->with('success', 'Potensi berhasil diperbarui.');
    }

    public function hapusPotensi($id)
    {
        $kelompok = $this->authorizeKelompok();
        $potensi = PotensiKelompok::where('id', $id)->where('id_kelompok', $kelompok->id)->firstOrFail();
        if ($potensi->foto) {
            Storage::disk('public')->delete('potensi/' . $potensi->foto);
        }
        $potensi->delete();

        return redirect()->route('kelompok.potensi')->with('success', 'Potensi berhasil dihapus.');
    }

    /* ─── Permasalahan CRUD ─────────────────────────────────── */

    public function permasalahan()
    {
        $kelompok = $this->authorizeKelompok();
        $permasalahanList = PermasalahanKelompok::where('id_kelompok', $kelompok->id)->orderByDesc('created_at')->get();
        return view('kelompok.kelompok_permasalahan', compact('kelompok', 'permasalahanList') + $this->dataNavigasi());
    }

    public function tambahPermasalahan()
    {
        $kelompok = $this->authorizeKelompok();
        return view('kelompok.kelompok_tambah_permasalahan', compact('kelompok') + $this->dataNavigasi());
    }

    public function simpanPermasalahan(Request $request)
    {
        $kelompok = $this->authorizeKelompok();

        $data = $request->validate([
            'judul'     => ['required', 'string', 'max:200'],
            'deskripsi' => ['nullable', 'string'],
            'status'    => ['nullable', 'in:Aktif,Selesai'],
        ]);

        PermasalahanKelompok::create(array_merge($data, [
            'id_kelompok' => $kelompok->id,
            'status'      => $data['status'] ?? 'Aktif',
        ]));

        return redirect()->route('kelompok.permasalahan')->with('success', 'Permasalahan berhasil ditambahkan.');
    }

    public function editPermasalahan($id)
    {
        $kelompok = $this->authorizeKelompok();
        $permasalahan = $this->checkOwnership($kelompok->id, $id, 'permasalahan_kelompok');
        if (!$permasalahan) {
            return redirect()->route('kelompok.permasalahan')->with('error', 'Permasalahan tidak ditemukan.');
        }

        // Cek jika view terstruktur per folder atau file flat
        if (view()->exists('kelompok.permasalahan.edit')) {
            return view('kelompok.permasalahan.edit', compact('kelompok', 'permasalahan') + $this->dataNavigasi());
        }

        return view('kelompok.kelompok_edit_permasalahan', compact('kelompok', 'permasalahan') + $this->dataNavigasi());
    }

    public function updatePermasalahan(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompok();
        $permasalahan = $this->checkOwnership($kelompok->id, $id, 'permasalahan_kelompok');
        if (!$permasalahan) {
            return redirect()->route('kelompok.permasalahan')->with('error', 'Permasalahan tidak ditemukan.');
        }

        $data = $request->validate([
            'judul'     => ['required', 'string', 'max:200'],
            'deskripsi' => ['nullable', 'string'],
            'status'    => ['required', 'in:Aktif,Selesai'],
        ]);

        PermasalahanKelompok::where('id', $id)->update($data);

        return redirect()->route('kelompok.permasalahan')->with('success', 'Permasalahan berhasil diperbarui.');
    }

    public function hapusPermasalahan($id)
    {
        $kelompok = $this->authorizeKelompok();
        $permasalahan = $this->checkOwnership($kelompok->id, $id, 'permasalahan_kelompok');
        if (!$permasalahan) {
            return redirect()->route('kelompok.permasalahan')->with('error', 'Permasalahan tidak ditemukan.');
        }
        PermasalahanKelompok::where('id', $id)->delete();

        return redirect()->route('kelompok.permasalahan')->with('success', 'Permasalahan berhasil dihapus.');
    }
}
