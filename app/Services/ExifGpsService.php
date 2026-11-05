<?php

namespace App\Services;

use DateTimeInterface;
use DateTime;
use DateTimeZone;

class ExifGpsService
{
    /**
     * Menyisipkan metadata EXIF GPS (Latitude, Longitude, Altitude, Timestamp)
     * langsung ke dalam binary file JPEG.
     *
     * @param string $jpegData Binary data gambar JPEG
     * @param float $latitude Garis lintang (misal: -7.655234)
     * @param float $longitude Garis bujur (misal: 111.528097)
     * @param float|null $altitude Ketinggian mdpl (misal: 450.5)
     * @param DateTimeInterface|null $datetime Waktu pengambilan
     * @return string Binary JPEG yang sudah memiliki tag EXIF GPS
     */
    public static function injectGps(
        string $jpegData,
        float $latitude,
        float $longitude,
        ?float $altitude = null,
        ?DateTimeInterface $datetime = null
    ): string {
        // Pastikan ini file JPEG yang valid (dimulai dengan SOI marker 0xFF 0xD8)
        if (strlen($jpegData) < 4 || substr($jpegData, 0, 2) !== "\xFF\xD8") {
            return $jpegData;
        }

        $datetime = $datetime ?? new DateTime('now', new DateTimeZone('UTC'));
        $app1Segment = self::buildApp1GpsSegment($latitude, $longitude, $altitude, $datetime);

        // Rekonstruksi JPEG: SOI (0xFFD8) + APP1 EXIF + sisa file JPEG
        $pos = 2;
        $len = strlen($jpegData);
        $newJpeg = "\xFF\xD8" . $app1Segment;

        while ($pos < $len - 1) {
            if (ord($jpegData[$pos]) === 0xFF) {
                $marker = ord($jpegData[$pos + 1]);

                // Marker segmen aplikasi APP0-APP15 (0xE0 - 0xEF) atau komentar COM (0xFE)
                if (($marker >= 0xE0 && $marker <= 0xEF) || $marker === 0xFE) {
                    $segLen = (ord($jpegData[$pos + 2]) << 8) + ord($jpegData[$pos + 3]);

                    // Jika segmen lama adalah APP1 (0xE1) Exif, ganti dengan segmen baru
                    if ($marker === 0xE1 && substr($jpegData, $pos + 4, 4) === "Exif") {
                        $pos += 2 + $segLen;
                        continue;
                    }

                    // Pertahankan segmen lain (misalnya JFIF APP0)
                    $newJpeg .= substr($jpegData, $pos, 2 + $segLen);
                    $pos += 2 + $segLen;
                    continue;
                }

                // Setelah melewati segmen header (misal SOS / DQT / SOF), sertakan seluruh sisa file
                $newJpeg .= substr($jpegData, $pos);
                break;
            } else {
                $pos++;
            }
        }

        return $newJpeg;
    }

    /**
     * Membangun segmen APP1 EXIF berstandar TIFF Little-Endian (II)
     */
    private static function buildApp1GpsSegment(
        float $latitude,
        float $longitude,
        ?float $altitude,
        DateTimeInterface $datetime
    ): string {
        $latRef = $latitude >= 0 ? 'N' : 'S';
        $lat = abs($latitude);
        $latDeg = floor($lat);
        $latMin = floor(($lat - $latDeg) * 60);
        $latSec = round((($lat - $latDeg) * 60 - $latMin) * 60 * 10000);

        $lonRef = $longitude >= 0 ? 'E' : 'W';
        $lon = abs($longitude);
        $lonDeg = floor($lon);
        $lonMin = floor(($lon - $lonDeg) * 60);
        $lonSec = round((($lon - $lonDeg) * 60 - $lonMin) * 60 * 10000);

        $altRef = ($altitude !== null && $altitude < 0) ? 1 : 0;
        $altVal = round(abs($altitude ?? 0) * 100);

        $dateStr = $datetime->format('Y:m:d');
        $timeHours = (int)$datetime->format('H');
        $timeMins = (int)$datetime->format('i');
        $timeSecs = (int)$datetime->format('s');
        $dateTimeStr = $datetime->format('Y:m:d H:i:s') . "\0";

        $numIfd0Entries = 3;
        $numGpsEntries = 9;

        // Header TIFF = 8 byte
        $ifd0Offset = 8;
        $ifd0Size = 2 + ($numIfd0Entries * 12) + 4;
        $gpsIfdOffset = $ifd0Offset + $ifd0Size;
        $gpsIfdSize = 2 + ($numGpsEntries * 12) + 4;
        $dataOffset = $gpsIfdOffset + $gpsIfdSize;

        $dataBuffer = '';
        $addData = function ($bytes) use (&$dataBuffer, $dataOffset) {
            $currentOffset = $dataOffset + strlen($dataBuffer);
            $dataBuffer .= $bytes;
            return $currentOffset;
        };

        // IFD0 String Values
        $softwareStr = "SIPSONLINE\0";
        $softwareOffset = $addData($softwareStr);
        $dateTimeOffset = $addData($dateTimeStr);

        // IFD0 Data (Count + Entries + Next IFD Pointer)
        $ifd0Data = pack('v', $numIfd0Entries);
        // 0x0131: Software (ASCII)
        $ifd0Data .= pack('vvVV', 0x0131, 2, strlen($softwareStr), $softwareOffset);
        // 0x0132: DateTime (ASCII)
        $ifd0Data .= pack('vvVV', 0x0132, 2, strlen($dateTimeStr), $dateTimeOffset);
        // 0x8825: GPSInfo IFD Pointer (LONG)
        $ifd0Data .= pack('vvVV', 0x8825, 4, 1, $gpsIfdOffset);
        $ifd0Data .= pack('V', 0); // Next IFD = 0

        // GPS Value Buffers
        $latRational = pack('VVVVVV', $latDeg, 1, $latMin, 1, $latSec, 10000);
        $latOffset = $addData($latRational);

        $lonRational = pack('VVVVVV', $lonDeg, 1, $lonMin, 1, $lonSec, 10000);
        $lonOffset = $addData($lonRational);

        $altRational = pack('VV', $altVal, 100);
        $altOffset = $addData($altRational);

        $timeRational = pack('VVVVVV', $timeHours, 1, $timeMins, 1, $timeSecs, 1);
        $timeOffset = $addData($timeRational);

        $dateStrTerm = $dateStr . "\0";
        $dateOffset = $addData($dateStrTerm);

        // GPS IFD Entries (12 bytes per entry)
        $gpsIfdData = pack('v', $numGpsEntries);
        // 0x0000: GPSVersionID (BYTE, 4)
        $gpsIfdData .= pack('vvVCCCC', 0x0000, 1, 4, 2, 3, 0, 0);
        // 0x0001: GPSLatitudeRef (ASCII, 2 padded to 4)
        $gpsIfdData .= pack('vvVa4', 0x0001, 2, 2, $latRef . "\0\0\0");
        // 0x0002: GPSLatitude (RATIONAL, 3)
        $gpsIfdData .= pack('vvVV', 0x0002, 5, 3, $latOffset);
        // 0x0003: GPSLongitudeRef (ASCII, 2 padded to 4)
        $gpsIfdData .= pack('vvVa4', 0x0003, 2, 2, $lonRef . "\0\0\0");
        // 0x0004: GPSLongitude (RATIONAL, 3)
        $gpsIfdData .= pack('vvVV', 0x0004, 5, 3, $lonOffset);
        // 0x0005: GPSAltitudeRef (BYTE, 1)
        $gpsIfdData .= pack('vvVCCCC', 0x0005, 1, 1, $altRef, 0, 0, 0);
        // 0x0006: GPSAltitude (RATIONAL, 1)
        $gpsIfdData .= pack('vvVV', 0x0006, 5, 1, $altOffset);
        // 0x0007: GPSTimeStamp (RATIONAL, 3)
        $gpsIfdData .= pack('vvVV', 0x0007, 5, 3, $timeOffset);
        // 0x001D: GPSDateStamp (ASCII, 11)
        $gpsIfdData .= pack('vvVV', 0x001D, 2, strlen($dateStrTerm), $dateOffset);
        $gpsIfdData .= pack('V', 0); // Next IFD = 0

        // TIFF Header: "II" (Little Endian) + 42 (0x002A) + Offset IFD0 (8)
        $tiffHeader = "II\x2A\x00\x08\x00\x00\x00";

        $exifBody = $tiffHeader . $ifd0Data . $gpsIfdData . $dataBuffer;
        $app1Len = 2 + 6 + strlen($exifBody); // 2 bytes length + "Exif\0\0" (6) + body

        return "\xFF\xE1" . pack('n', $app1Len) . "Exif\x00\x00" . $exifBody;
    }
}
