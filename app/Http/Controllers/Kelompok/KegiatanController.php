<?php

namespace App\Http\Controllers\Kelompok;

use App\Models\Kegiatan;
use App\Models\DokumentasiKegiatan;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KegiatanController extends BaseKelompokController
{
    public function kegiatan()
    {
        $kelompok = $this->authorizeKelompok();
        $kegiatanList = Kegiatan::where('id_kelompok', $kelompok->id)->orderByDesc('tanggal')->get();
        return view('kelompok.kelompok_kegiatan', compact('kelompok', 'kegiatanList') + $this->dataNavigasi());
    }

    public function tambahKegiatan()
    {
        $kelompok = $this->authorizeKelompok();
        return view('kelompok.kelompok_tambah_kegiatan', compact('kelompok') + $this->dataNavigasi());
    }

    public function simpanKegiatan(Request $request)
    {
        $kelompok = $this->authorizeKelompok();

        $data = $request->validate([
            'nama_kegiatan' => ['required', 'string', 'max:200'],
            'tanggal'       => ['required', 'date'],
            'lokasi'        => ['nullable', 'string', 'max:255'],
            'keterangan'    => ['nullable', 'string'],
            'kml_file'      => ['nullable', 'file', 'mimes:kml', 'max:10240'],
        ]);

        $kmlFilename = null;
        if ($request->hasFile('kml_file')) {
            $file = $request->file('kml_file');
            $kmlFilename = time() . '_' . Str::random(12) . '.kml';
            $file->storeAs('kegiatan', $kmlFilename, 'public');
        }

        Kegiatan::create(array_merge($data, [
            'id_kelompok' => $kelompok->id,
            'kml_file'    => $kmlFilename,
        ]));

        return redirect()->route('kelompok.kegiatan')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function detailKegiatan($id)
    {
        $kelompok = $this->authorizeKelompok();
        $kegiatan = $this->checkOwnership($kelompok->id, $id, 'kegiatan');
        if (!$kegiatan) {
            return redirect()->route('kelompok.kegiatan')->with('error', 'Kegiatan tidak ditemukan.');
        }
        $dokumentasi = DokumentasiKegiatan::where('id_kegiatan', $id)->get();

        $kmlUrl = '';
        if (!empty($kegiatan->kml_file)) {
            if (file_exists(public_path('storage/kegiatan/' . $kegiatan->kml_file))) {
                $kmlUrl = asset('storage/kegiatan/' . $kegiatan->kml_file);
            } elseif (file_exists(public_path('uploads/kml_kegiatan/' . $kegiatan->kml_file))) {
                $kmlUrl = asset('uploads/kml_kegiatan/' . rawurlencode($kegiatan->kml_file));
            } elseif (file_exists(storage_path('app/public/kegiatan/' . $kegiatan->kml_file))) {
                $kmlUrl = asset('storage/kegiatan/' . $kegiatan->kml_file);
            }
        }

        return view('kelompok.kelompok_detail_kegiatan', compact('kelompok', 'kegiatan', 'dokumentasi', 'kmlUrl') + $this->dataNavigasi());
    }

    public function petaKegiatan($id)
    {
        $kelompok = $this->authorizeKelompok();
        $kegiatan = $this->checkOwnership($kelompok->id, $id, 'kegiatan');
        if (!$kegiatan) {
            return redirect()->route('kelompok.kegiatan')->with('error', 'Kegiatan tidak ditemukan.');
        }

        $dokumentasi = DokumentasiKegiatan::where('id_kegiatan', $id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('id', 'asc')
            ->get();

        $kmlUrl = '';
        if (!empty($kegiatan->kml_file)) {
            if (file_exists(public_path('storage/kegiatan/' . $kegiatan->kml_file))) {
                $kmlUrl = asset('storage/kegiatan/' . $kegiatan->kml_file);
            } elseif (file_exists(public_path('uploads/kml_kegiatan/' . $kegiatan->kml_file))) {
                $kmlUrl = asset('uploads/kml_kegiatan/' . rawurlencode($kegiatan->kml_file));
            } elseif (file_exists(storage_path('app/public/kegiatan/' . $kegiatan->kml_file))) {
                $kmlUrl = asset('storage/kegiatan/' . $kegiatan->kml_file);
            }
        }

        return view('kelompok.kelompok_peta_kegiatan', compact('kelompok', 'kegiatan', 'dokumentasi', 'kmlUrl') + $this->dataNavigasi());
    }

    public function editKegiatan($id)
    {
        $kelompok = $this->authorizeKelompok();
        $kegiatan = $this->checkOwnership($kelompok->id, $id, 'kegiatan');
        if (!$kegiatan) {
            return redirect()->route('kelompok.kegiatan')->with('error', 'Kegiatan tidak ditemukan.');
        }
        return view('kelompok.kelompok_edit_kegiatan', compact('kelompok', 'kegiatan') + $this->dataNavigasi());
    }

    public function updateKegiatan(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompok();
        $kegiatan = $this->checkOwnership($kelompok->id, $id, 'kegiatan');
        if (!$kegiatan) {
            return redirect()->route('kelompok.kegiatan')->with('error', 'Kegiatan tidak ditemukan.');
        }

        $data = $request->validate([
            'nama_kegiatan' => ['required', 'string', 'max:200'],
            'tanggal'       => ['required', 'date'],
            'lokasi'        => ['nullable', 'string', 'max:255'],
            'keterangan'    => ['nullable', 'string'],
            'kml_file'      => ['nullable', 'file', 'mimes:kml', 'max:10240'],
        ]);

        $kmlFilename = $kegiatan->kml_file;
        if ($request->hasFile('kml_file')) {
            if ($kegiatan->kml_file) {
                Storage::disk('public')->delete('kegiatan/' . $kegiatan->kml_file);
            }
            $file = $request->file('kml_file');
            $kmlFilename = time() . '_' . Str::random(12) . '.kml';
            $file->storeAs('kegiatan', $kmlFilename, 'public');
        }

        $data['kml_file'] = $kmlFilename;
        Kegiatan::where('id', $id)->update($data);

        return redirect()->route('kelompok.kegiatan')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function hapusKegiatan($id)
    {
        $kelompok = $this->authorizeKelompok();
        $kegiatan = $this->checkOwnership($kelompok->id, $id, 'kegiatan');
        if (!$kegiatan) {
            return redirect()->route('kelompok.kegiatan')->with('error', 'Kegiatan tidak ditemukan.');
        }

        if ($kegiatan->kml_file) {
            Storage::disk('public')->delete('kegiatan/' . $kegiatan->kml_file);
        }

        DokumentasiKegiatan::where('id_kegiatan', $id)->delete();
        Kegiatan::where('id', $id)->delete();

        return redirect()->route('kelompok.kegiatan')->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function simpanDokumentasi(Request $request, $idKegiatan)
    {
        $kelompok = $this->authorizeKelompok();
        $kegiatan = $this->checkOwnership($kelompok->id, $idKegiatan, 'kegiatan');
        if (!$kegiatan) {
            return redirect()->route('kelompok.kegiatan')->with('error', 'Kegiatan tidak ditemukan.');
        }

        $request->validate([
            'foto'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:15360'],
            'foto_base64' => ['nullable', 'string'],
            'keterangan'  => ['nullable', 'string', 'max:500'],
            'latitude'    => ['nullable', 'string', 'max:50'],
            'longitude'   => ['nullable', 'string', 'max:50'],
            'altitude'    => ['nullable', 'string', 'max:50'],
            'akurasi'     => ['nullable', 'string', 'max:50'],
        ]);

        // Buat nama file standar SIPSONLINE (Contoh: Csrb_20260821_182425_3624.jpg)
        $prefix = 'Foto';
        if (!empty($kegiatan->nama_kegiatan)) {
            $clean = preg_replace('/[^a-zA-Z0-9]/', '', $kegiatan->nama_kegiatan);
            if (strlen($clean) >= 4) {
                $prefix = ucfirst(strtolower(substr($clean, 0, 4)));
            } elseif (strlen($clean) > 0) {
                $prefix = ucfirst(strtolower($clean));
            }
        }
        $filename = $prefix . '_' . now()->format('Ymd_His') . '_' . mt_rand(1000, 9999) . '.jpg';
        $savedFile = false;

        $lat = $request->input('latitude');
        $lng = $request->input('longitude');
        $alt = $request->input('altitude');
        $acc = $request->input('akurasi');

        // If base64 photo with stamped canvas watermark is provided
        if ($request->filled('foto_base64')) {
            $base64Data = $request->input('foto_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                $imageData = base64_decode($base64Data);
                if ($imageData !== false) {
                    // Pastikan ukuran di bawah 300 KB (307.200 bytes)
                    if (strlen($imageData) > 307200 && extension_loaded('gd')) {
                        $src = @imagecreatefromstring($imageData);
                        if ($src) {
                            $quality = 75;
                            do {
                                ob_start();
                                imagejpeg($src, null, $quality);
                                $comp = ob_get_clean();
                                $quality -= 10;
                            } while (strlen($comp) > 307200 && $quality >= 30);
                            imagedestroy($src);
                            if (strlen($comp) > 0) {
                                $imageData = $comp;
                            }
                        }
                    }

                    // Tanamkan metadata EXIF GPS langsung ke file JPEG
                    if ($lat && $lng) {
                        try {
                            $imageData = \App\Services\ExifGpsService::injectGps(
                                $imageData,
                                (float)$lat,
                                (float)$lng,
                                $alt ? (float)$alt : null,
                                now()
                            );
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::warning('[ExifGPS] Error inject GPS: ' . $e->getMessage());
                        }
                    }

                    Storage::disk('public')->put('kegiatan/' . $filename, $imageData);
                    $savedFile = true;
                }
            }
        }

        // Fallback to uploaded file
        if (!$savedFile && $request->hasFile('foto')) {
            $file = $request->file('foto');
            $rawContent = file_get_contents($file->getRealPath());

            // Kompresi jika di atas 300 KB
            if (strlen($rawContent) > 307200 && extension_loaded('gd')) {
                $src = @imagecreatefromstring($rawContent);
                if ($src) {
                    $quality = 75;
                    do {
                        ob_start();
                        imagejpeg($src, null, $quality);
                        $comp = ob_get_clean();
                        $quality -= 10;
                    } while (strlen($comp) > 307200 && $quality >= 30);
                    imagedestroy($src);
                    if (strlen($comp) > 0) {
                        $rawContent = $comp;
                    }
                }
            }

            // Tanamkan metadata EXIF GPS langsung ke file JPEG
            if ($lat && $lng) {
                try {
                    $rawContent = \App\Services\ExifGpsService::injectGps(
                        $rawContent,
                        (float)$lat,
                        (float)$lng,
                        $alt ? (float)$alt : null,
                        now()
                    );
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('[ExifGPS] Error inject GPS: ' . $e->getMessage());
                }
            }

            Storage::disk('public')->put('kegiatan/' . $filename, $rawContent);
            $savedFile = true;
        }

        if (!$savedFile) {
            return back()->with('error', 'Gagal memproses foto. Pastikan Anda telah memilih atau mengambil foto.');
        }

        $pubKegiatanDir = public_path('storage/kegiatan');
        if (!is_dir($pubKegiatanDir)) {
            @mkdir($pubKegiatanDir, 0777, true);
        }
        $pubKegiatanFile = $pubKegiatanDir . '/' . $filename;
        if (!file_exists($pubKegiatanFile)) {
            @copy(storage_path('app/public/kegiatan/' . $filename), $pubKegiatanFile);
        }

        // Format keterangan / geotag info (Latitude, Longitude, Altitude)
        $ket = $request->input('keterangan', '');
        $lat = $request->input('latitude');
        $lng = $request->input('longitude');
        $alt = $request->input('altitude');
        $acc = $request->input('akurasi');

        $geoParts = [];
        if ($lat && $lng) {
            $geoParts[] = "📍 GPS: {$lat}, {$lng}";
        }
        if ($alt) {
            $geoParts[] = "🏔️ Alt: {$alt} m mdpl";
        }
        if (count($geoParts) > 0) {
            $ket = ($ket ? $ket . ' | ' : '') . implode(' | ', $geoParts);
        }

        $file = $request->file('foto');
        $fileSize = isset($file) ? $file->getSize() : (isset($imageData) ? strlen($imageData) : null);
        $metadata = [
            'ip'            => $request->ip(),
            'browser'       => $request->userAgent(),
            'ukuran'        => $fileSize,
            'mime'          => isset($file) ? $file->getMimeType() : 'image/jpeg',
            'nama_kelompok' => $kelompok->nama_kelompok ?? null,
            'nama_kegiatan' => $kegiatan->nama_kegiatan ?? null,
        ];

        $doc = DokumentasiKegiatan::create([
            'id_kegiatan'         => $idKegiatan,
            'id_user'             => auth()->id() ?? 0,
            'foto_uuid'           => (string) Str::uuid(),
            'nama_file'           => $filename,
            'file_path'           => 'storage/kegiatan/' . $filename,
            'file_type'           => isset($file) ? $file->getMimeType() : 'image/jpeg',
            'metadata'            => json_encode($metadata),
            'keterangan'          => $request->input('keterangan'),
            'latitude'            => $lat ? (float)$lat : null,
            'longitude'           => $lng ? (float)$lng : null,
            'altitude'            => $alt ? (float)$alt : null,
            'akurasi'             => $request->filled('akurasi') ? (float)$acc : 17.00,
            'tanggal_foto'        => now(),
            'status_geotag'       => ($lat && $lng) ? 'KAMERA' : 'TIDAK_ADA',
            'google_drive_status' => 'pending',
            'created_at'          => now(),
        ]);

        // Auto-upload ke Google Drive secara aman
        try {
            app(\App\Services\GoogleDriveService::class)->syncDokumentasi($doc);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('[GoogleDrive] Auto-sync error: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Foto dokumentasi kegiatan berhasil diunggah dan disinkronkan ke Google Drive.',
                'doc_id'       => $doc->id,
                'drive_status' => $doc->google_drive_status,
                'nama_file'    => $doc->nama_file,
            ]);
        }

        return redirect()->route('kelompok.kegiatan.detail', $idKegiatan)
            ->with('success', 'Foto dokumentasi kegiatan berhasil diunggah dan disinkronkan ke Google Drive.');
    }

    public function syncDriveDokumentasi($idDokumentasi)
    {
        $kelompok = $this->authorizeKelompok();
        $doc = DokumentasiKegiatan::find($idDokumentasi);
        if (!$doc) {
            return back()->with('error', 'Dokumentasi tidak ditemukan.');
        }

        $kegiatan = $this->checkOwnership($kelompok->id, $doc->id_kegiatan, 'kegiatan');
        if (!$kegiatan) {
            return back()->with('error', 'Akses ditolak.');
        }

        $driveService = app(\App\Services\GoogleDriveService::class);
        $success = $driveService->syncDokumentasi($doc);

        if ($success) {
            return back()->with('success', 'Foto berhasil disinkronkan ke Google Drive!');
        }

        return back()->with('error', 'Gagal sinkronisasi: ' . ($doc->google_drive_error ?: 'Koneksi bermasalah'));
    }

    public function hapusDokumentasi($idDokumentasi)
    {
        $kelompok = $this->authorizeKelompok();
        $doc = DokumentasiKegiatan::find($idDokumentasi);
        if (!$doc) {
            return back()->with('error', 'Dokumentasi tidak ditemukan.');
        }

        $kegiatan = $this->checkOwnership($kelompok->id, $doc->id_kegiatan, 'kegiatan');
        if (!$kegiatan) {
            return back()->with('error', 'Akses ditolak.');
        }

        $filename = $doc->nama_file ?? $doc->foto;
        if ($filename) {
            Storage::disk('public')->delete('kegiatan/' . $filename);
            if (file_exists(public_path('storage/kegiatan/' . $filename))) {
                @unlink(public_path('storage/kegiatan/' . $filename));
            }
        }
        if ($doc->file_path && file_exists(public_path($doc->file_path))) {
            @unlink(public_path($doc->file_path));
        }
        $doc->delete();

        return back()->with('success', 'Foto dokumentasi berhasil dihapus.');
    }
}

