<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetakPerhutani extends Model
{
    protected $table = 'petak_perhutani';

    public $timestamps = false;

    protected $fillable = [
        'id_kelompok',
        'nama_petak',
        'luas_ha',
        'jenis_hutan',
        'skema_ps',
        'tahun_mulai',
        'tahun_selesai',
        'keterangan',
        'polygon',
    ];

    protected $casts = [
        'luas_ha' => 'decimal:2',
        'polygon' => 'array',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok');
    }

    /**
     * Ambil Petak Perhutani berdasarkan poligon atau wilayah kelompok
     */
    public static function getForKelompok($kelompok)
    {
        if (!$kelompok) {
            return collect();
        }

        $kolomSelect = [
            'id', 'kode_toponimi', 'petak', 'anak_petak', 'rph', 'bkph', 'kph',
            'desa', 'kecamatan', 'kabupaten', 'fungsi', 'klas_hutan', 'luas_baku',
            'jenis_tanaman', 'tahun_tanam', 'atribut',
            \Illuminate\Support\Facades\DB::raw('ST_AsGeoJSON(geometry) as geojson'),
        ];

        // 1. Prioritas Utama: Bounding Box Polygon Kelompok
        if (!empty($kelompok->polygon_kelompok)) {
            try {
                $json = is_array($kelompok->polygon_kelompok)
                    ? $kelompok->polygon_kelompok
                    : json_decode($kelompok->polygon_kelompok, true);

                $minX = 999; $minY = 999; $maxX = -999; $maxY = -999;
                $scan = function ($obj) use (&$scan, &$minX, &$minY, &$maxX, &$maxY) {
                    if (is_array($obj)) {
                        if (isset($obj[0], $obj[1]) && is_numeric($obj[0]) && is_numeric($obj[1])) {
                            $x = (float)$obj[0];
                            $y = (float)$obj[1];
                            if ($x < $minX) $minX = $x;
                            if ($x > $maxX) $maxX = $x;
                            if ($y < $minY) $minY = $y;
                            if ($y > $maxY) $maxY = $y;
                            return;
                        }
                        foreach ($obj as $v) {
                            $scan($v);
                        }
                    }
                };
                $scan($json);

                if ($minX < 999) {
                    $buffer = 0.003; // Tambahkan buffer ~300 meter sekitar kelompok
                    $minX -= $buffer;
                    $minY -= $buffer;
                    $maxX += $buffer;
                    $maxY += $buffer;

                    $sql = "
                        SELECT id, kode_toponimi, petak, anak_petak, rph, bkph, kph,
                               desa, kecamatan, kabupaten, fungsi, klas_hutan, luas_baku,
                               jenis_tanaman, tahun_tanam, atribut,
                               ST_AsGeoJSON(geometry) AS geojson
                        FROM petak_perhutani
                        WHERE MBRIntersects(
                            geometry,
                            ST_GeomFromText('POLYGON(($minX $minY, $maxX $minY, $maxX $maxY, $minX $maxY, $minX $minY))')
                        )
                        ORDER BY kode_toponimi
                    ";

                    $petakSpatial = \Illuminate\Support\Facades\DB::select($sql);
                    if (!empty($petakSpatial)) {
                        return collect($petakSpatial);
                    }
                }
            } catch (\Throwable $e) {
                // Fallback jika query spasial gagal
            }
        }

        // 2. Fallback: Filter per desa, kecamatan, kabupaten
        try {
            $petakQuery = \Illuminate\Support\Facades\DB::table('petak_perhutani');

            if (!empty($kelompok->desa)) {
                $petakQuery->where('desa', 'LIKE', '%' . trim($kelompok->desa) . '%');
            } elseif (!empty($kelompok->kecamatan)) {
                $petakQuery->where('kecamatan', 'LIKE', '%' . trim($kelompok->kecamatan) . '%');
            } elseif (!empty($kelompok->kabupaten)) {
                $petakQuery->where('kabupaten', 'LIKE', '%' . trim($kelompok->kabupaten) . '%');
            }

            $petakPerhutani = $petakQuery->select($kolomSelect)->limit(100)->get();

            if ($petakPerhutani->isEmpty()) {
                $kab = !empty($kelompok->kabupaten) ? trim($kelompok->kabupaten) : 'Madiun';
                $petakPerhutani = \Illuminate\Support\Facades\DB::table('petak_perhutani')
                    ->where('kabupaten', 'LIKE', '%' . $kab . '%')
                    ->select($kolomSelect)
                    ->limit(80)
                    ->get();
            }

            return $petakPerhutani;
        } catch (\Throwable $e) {
            return collect();
        }
    }
}
