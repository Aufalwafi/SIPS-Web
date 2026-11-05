<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kelompok Belum Ada Pendamping</title>
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
    <h2>Daftar Kelompok Belum Ada Pendamping</h2>
    <table>
        <thead>
            <tr>
                <th width="50" class="text-center">No</th>
                <th>Nama Kelompok</th>
                <th>Desa</th>
                <th width="120" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kelompokKosong as $no => $k)
            <tr>
                <td class="text-center">{{ $no + 1 }}</td>
                <td>{{ $k->nama_kelompok }}</td>
                <td>{{ $k->desa }}</td>
                <td class="text-center" style="color: red; font-weight: bold;">Belum Ada</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Semua kelompok sudah memiliki pendamping.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>