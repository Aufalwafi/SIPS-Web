<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;

class GeoParserService
{
    /**
     * Memproses berkas spasial (KML atau GeoJSON) dan mengembalikan string JSON standar GeoJSON.
     *
     * @param UploadedFile|string $file
     * @return string
     * @throws Exception
     */
    public function parseToGeoJson(UploadedFile|string $file): string
    {
        $content = '';
        $extension = '';

        if ($file instanceof UploadedFile) {
            $content = file_get_contents($file->getRealPath());
            $extension = strtolower($file->getClientOriginalExtension());
            if (!$extension) {
                $extension = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
            }
        } else {
            $content = $file;
            $extension = str_starts_with(trim($content), '<') ? 'kml' : 'json';
        }

        $content = trim($content);
        if (empty($content)) {
            throw new Exception('Berkas spasial kosong.');
        }

        // Jika diawali dengan '<', anggap sebagai XML / KML
        if ($extension === 'kml' || str_starts_with($content, '<?xml') || str_contains($content, '<kml')) {
            return $this->parseKml($content);
        }

        // Default: parse sebagai GeoJSON / JSON
        return $this->parseGeoJson($content);
    }

    /**
     * Memvalidasi dan menormalkan GeoJSON agar berformat FeatureCollection standar.
     */
    protected function parseGeoJson(string $jsonString): string
    {
        $data = json_decode($jsonString, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            throw new Exception('Format JSON tidak valid: ' . json_last_error_msg());
        }

        if (!isset($data['type'])) {
            throw new Exception('Format GeoJSON tidak valid: Atribut "type" tidak ditemukan.');
        }

        $type = $data['type'];

        // Jika sudah FeatureCollection
        if ($type === 'FeatureCollection') {
            if (!isset($data['features']) || !is_array($data['features'])) {
                throw new Exception('Format GeoJSON FeatureCollection tidak memiliki array "features".');
            }
            return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        // Jika berupa Feature tunggal
        if ($type === 'Feature') {
            $normalized = [
                'type' => 'FeatureCollection',
                'features' => [$data],
            ];
            return json_encode($normalized, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        // Jika berupa geometri murni (Polygon, MultiPolygon, GeometryCollection, dll)
        if (in_array($type, ['Polygon', 'MultiPolygon', 'GeometryCollection', 'Point', 'LineString'])) {
            $normalized = [
                'type' => 'FeatureCollection',
                'features' => [
                    [
                        'type' => 'Feature',
                        'geometry' => $data,
                        'properties' => [],
                    ]
                ],
            ];
            return json_encode($normalized, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        // Tipe tidak dikenal
        throw new Exception("Tipe GeoJSON '{$type}' tidak didukung.");
    }

    /**
     * Mengonversi KML string menjadi GeoJSON FeatureCollection.
     */
    protected function parseKml(string $kmlString): string
    {
        $prevErrors = libxml_use_internal_errors(true);

        try {
            $dom = new \DOMDocument();
            $loaded = $dom->loadXML($kmlString, LIBXML_NOBLANKS | LIBXML_NONET);
            if (!$loaded) {
                $err = libxml_get_last_error();
                throw new Exception('Gagal membaca berkas KML XML: ' . ($err ? $err->message : 'Format XML tidak valid.'));
            }

            $xpath = new \DOMXPath($dom);
            // Registrasi namespace KML jika ada
            $rootNamespace = $dom->documentElement->namespaceURI;
            if ($rootNamespace) {
                $xpath->registerNamespace('kml', $rootNamespace);
                $prefix = 'kml:';
            } else {
                $prefix = '';
            }

            $placemarks = $xpath->query("//{$prefix}Placemark");
            $features = [];

            if ($placemarks && $placemarks->length > 0) {
                foreach ($placemarks as $placemark) {
                    $feature = $this->parsePlacemark($placemark, $xpath, $prefix);
                    if ($feature) {
                        $features[] = $feature;
                    }
                }
            } else {
                // Jika tidak ada Placemark tapi ada Polygon langsung
                $polygons = $xpath->query("//{$prefix}Polygon");
                foreach ($polygons as $poly) {
                    $geom = $this->parsePolygonNode($poly, $xpath, $prefix);
                    if ($geom) {
                        $features[] = [
                            'type' => 'Feature',
                            'geometry' => $geom,
                            'properties' => [],
                        ];
                    }
                }
            }

            if (empty($features)) {
                throw new Exception('Tidak ditemukan koordinat poligon/geometri yang valid di dalam berkas KML.');
            }

            $geoJson = [
                'type' => 'FeatureCollection',
                'features' => $features,
            ];

            return json_encode($geoJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } finally {
            libxml_use_internal_errors($prevErrors);
        }
    }

    /**
     * Parsing elemen <Placemark>
     */
    protected function parsePlacemark(\DOMElement $placemark, \DOMXPath $xpath, string $prefix): ?array
    {
        $properties = [];

        // Ambil nama
        $nameNode = $xpath->query("{$prefix}name", $placemark)->item(0);
        if ($nameNode) {
            $properties['name'] = trim($nameNode->textContent);
        }

        // Ambil deskripsi
        $descNode = $xpath->query("{$prefix}description", $placemark)->item(0);
        if ($descNode) {
            $properties['description'] = trim($descNode->textContent);
        }

        // Ambil SimpleData dari ExtendedData
        $simpleDataNodes = $xpath->query(".//{$prefix}SimpleData", $placemark);
        if ($simpleDataNodes) {
            foreach ($simpleDataNodes as $sd) {
                $attrName = $sd->getAttribute('name');
                if ($attrName) {
                    $properties[$attrName] = trim($sd->textContent);
                }
            }
        }

        // Cari Polygon
        $polygonNodes = $xpath->query(".//{$prefix}Polygon", $placemark);
        if ($polygonNodes && $polygonNodes->length > 0) {
            if ($polygonNodes->length === 1) {
                $geom = $this->parsePolygonNode($polygonNodes->item(0), $xpath, $prefix);
                if ($geom) {
                    return [
                        'type' => 'Feature',
                        'geometry' => $geom,
                        'properties' => $properties,
                    ];
                }
            } else {
                // MultiPolygon
                $coordinatesList = [];
                foreach ($polygonNodes as $poly) {
                    $geom = $this->parsePolygonNode($poly, $xpath, $prefix);
                    if ($geom && isset($geom['coordinates'])) {
                        $coordinatesList[] = $geom['coordinates'];
                    }
                }
                if (!empty($coordinatesList)) {
                    return [
                        'type' => 'Feature',
                        'geometry' => [
                            'type' => 'MultiPolygon',
                            'coordinates' => $coordinatesList,
                        ],
                        'properties' => $properties,
                    ];
                }
            }
        }

        // Cari MultiGeometry
        $multiGeom = $xpath->query(".//{$prefix}MultiGeometry", $placemark)->item(0);
        if ($multiGeom) {
            $subPolygons = $xpath->query(".//{$prefix}Polygon", $multiGeom);
            if ($subPolygons && $subPolygons->length > 0) {
                $coordinatesList = [];
                foreach ($subPolygons as $poly) {
                    $geom = $this->parsePolygonNode($poly, $xpath, $prefix);
                    if ($geom && isset($geom['coordinates'])) {
                        $coordinatesList[] = $geom['coordinates'];
                    }
                }
                if (!empty($coordinatesList)) {
                    return [
                        'type' => 'Feature',
                        'geometry' => [
                            'type' => 'MultiPolygon',
                            'coordinates' => $coordinatesList,
                        ],
                        'properties' => $properties,
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Parsing elemen <Polygon> menjadi array struktur GeoJSON geometry
     */
    protected function parsePolygonNode(\DOMElement $polygon, \DOMXPath $xpath, string $prefix): ?array
    {
        $rings = [];

        // Outer Boundary
        $outerCoordNode = $xpath->query(".//{$prefix}outerBoundaryIs//{$prefix}coordinates", $polygon)->item(0);
        if (!$outerCoordNode) {
            // Fallback jika tidak ada tag outerBoundaryIs
            $outerCoordNode = $xpath->query(".//{$prefix}coordinates", $polygon)->item(0);
        }

        if ($outerCoordNode) {
            $outerCoords = $this->parseCoordinatesString($outerCoordNode->textContent);
            if (count($outerCoords) >= 3) {
                // Pastikan ring tertutup
                if ($outerCoords[0] !== $outerCoords[count($outerCoords) - 1]) {
                    $outerCoords[] = $outerCoords[0];
                }
                $rings[] = $outerCoords;
            }
        }

        if (empty($rings)) {
            return null;
        }

        // Inner Boundaries (holes)
        $innerNodes = $xpath->query(".//{$prefix}innerBoundaryIs//{$prefix}coordinates", $polygon);
        if ($innerNodes) {
            foreach ($innerNodes as $in) {
                $innerCoords = $this->parseCoordinatesString($in->textContent);
                if (count($innerCoords) >= 3) {
                    if ($innerCoords[0] !== $innerCoords[count($innerCoords) - 1]) {
                        $innerCoords[] = $innerCoords[0];
                    }
                    $rings[] = $innerCoords;
                }
            }
        }

        return [
            'type' => 'Polygon',
            'coordinates' => $rings,
        ];
    }

    /**
     * Mengubah string koordinat KML ("lng,lat,alt lng,lat,alt ...") menjadi array [[lng, lat], ...]
     */
    protected function parseCoordinatesString(string $coordString): array
    {
        $result = [];
        $rawTuples = preg_split('/[\s\r\n\t]+/', trim($coordString));

        foreach ($rawTuples as $tuple) {
            $tuple = trim($tuple);
            if ($tuple === '') continue;

            $parts = explode(',', $tuple);
            if (count($parts) >= 2) {
                $lng = floatval($parts[0]);
                $lat = floatval($parts[1]);
                // GeoJSON format: [longitude, latitude]
                $result[] = [$lng, $lat];
            }
        }

        return $result;
    }
}
