<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rekap Pendamping</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; }
        th { background: #eee; }
        .text-center { text-align: center; }
        @media print { body { padding: 0; } }
    </style>
</head>
<body onload="window.print()">
    <h2>Rekap Pendamping &amp; Kelompok Dampingan</h2>
    <table>
        <thead>
            <tr>
                <th width="50" class="text-center">No</th>
                <th>Nama Pendamping</th>
                <th>Username</th>
                <th>Kelompok</th>
                <th>Desa</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapPendamping as $no => $r)
            <tr>
                <td class="text-center">{{ $no + 1 }}</td>
                <td>{{ $r->nama ?? '-' }}</td>
                <td>{{ $r->username ?? '-' }}</td>
                <td>{{ $r->nama_kelompok ?? '-' }}</td>
                <td>{{ $r->desa ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">Belum ada data rekap pendamping.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>