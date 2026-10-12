<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Survei - {{ $survey->title }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #1e293b; margin: 0; padding: 32px; font-size: 13px; background: #fff; }
        .wrap { max-width: 900px; margin: 0 auto; }
        header { display: flex; align-items: center; gap: 16px; border-bottom: 3px solid #047857; padding-bottom: 14px; }
        header img { width: 64px; height: 64px; object-fit: contain; }
        header h1 { margin: 0; font-size: 18px; color: #065f46; letter-spacing: .5px; }
        header p { margin: 2px 0 0; color: #64748b; font-size: 12px; }
        h2 { font-size: 15px; margin: 28px 0 10px; color: #065f46; border-bottom: 1px solid #d1fae5; padding-bottom: 6px; }
        .title { text-align: center; margin: 22px 0 4px; font-size: 17px; font-weight: bold; text-transform: uppercase; }
        .subtitle { text-align: center; color: #64748b; margin-bottom: 18px; }
        .grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
        .box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; }
        .box small { color: #64748b; display: block; font-size: 11px; }
        .box b { font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #ecfdf5; color: #065f46; font-size: 12px; }
        td.num, th.num { text-align: center; white-space: nowrap; }
        .bar { height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden; }
        .bar span { display: block; height: 100%; background: #059669; }
        ul { margin: 6px 0; padding-left: 18px; }
        .sign { margin-top: 48px; display: flex; justify-content: flex-end; text-align: center; }
        .sign div { width: 240px; }
        .actions { text-align: right; margin-bottom: 16px; }
        .actions button { background: #047857; color: #fff; border: 0; padding: 10px 18px; border-radius: 8px; font-weight: bold; cursor: pointer; }
        @media (max-width: 640px) { body { padding: 16px; } .grid { grid-template-columns: repeat(2, 1fr); } }
        @media print { .actions { display: none; } body { padding: 0; } h2 { break-after: avoid; } tr { break-inside: avoid; } }
    </style>
</head>
<body>
<div class="wrap">
    <div class="actions"><button onclick="window.print()">🖨 Cetak / Simpan PDF</button></div>

    <header>
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
        <div>
            <h1>PERUMDA AIR MINUM TIRTA PINANG</h1>
            <p>Kota Pangkalpinang · Sistem Informasi Survei Pelanggan (SIMTAG)</p>
        </div>
    </header>

    <p class="title">Laporan Hasil Survei</p>
    <p class="subtitle">{{ $survey->title }}<br>Dicetak {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>

    <div class="grid">
        <div class="box"><small>Total Responden</small><b>{{ $stats['total'] }}</b></div>
        <div class="box"><small>Skor Rata-rata</small><b>{{ $stats['average'] !== null ? number_format($stats['average'], 2, ',', '.') : '-' }}</b> / 5</div>
        <div class="box"><small>Indeks Kepuasan</small><b>{{ $stats['index'] !== null ? number_format($stats['index'], 1, ',', '.') : '-' }}</b></div>
        <div class="box"><small>Kategori</small><b style="font-size:14px">{{ $stats['label'] }}</b></div>
    </div>

    <h2>A. Hasil Penilaian per Aspek</h2>
    <table>
        <thead><tr><th class="num">No</th><th>Pertanyaan</th><th class="num">Jawaban</th><th class="num">Rata-rata</th><th class="num">Indeks</th><th style="width:22%">Kategori</th></tr></thead>
        <tbody>
        @php $no = 0; @endphp
        @foreach ($stats['questions']->where('model.question_type', 'rating') as $q)
            <tr>
                <td class="num">{{ ++$no }}</td>
                <td>{{ $q['model']->question_text }}</td>
                <td class="num">{{ $q['answered'] }}</td>
                <td class="num">{{ $q['average'] !== null ? number_format($q['average'], 2, ',', '.') : '-' }}</td>
                <td class="num">{{ $q['index'] ?? '-' }}</td>
                <td>{{ $q['label'] }}<div class="bar"><span style="width: {{ $q['index'] ?? 0 }}%"></span></div></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @php $choices = $stats['questions']->where('model.question_type', 'multiple_choice'); @endphp
    @if ($choices->isNotEmpty())
        <h2>B. Pertanyaan Pilihan Ganda</h2>
        @foreach ($choices as $q)
            @php $sum = max(1, $q['options']->sum()); @endphp
            <p style="margin: 12px 0 4px"><b>{{ $q['model']->question_text }}</b></p>
            <table>
                <thead><tr><th>Pilihan</th><th class="num">Jumlah</th><th class="num">Persentase</th></tr></thead>
                <tbody>
                @foreach ($q['options']->sortDesc() as $option => $count)
                    <tr><td>{{ $option }}</td><td class="num">{{ $count }}</td><td class="num">{{ round($count / $sum * 100, 1) }}%</td></tr>
                @endforeach
                </tbody>
            </table>
        @endforeach
    @endif

    <h2>C. Kepuasan per Kecamatan</h2>
    <table>
        <thead><tr><th>Kecamatan</th><th class="num">Responden</th><th class="num">Skor Rata-rata</th><th class="num">Indeks</th></tr></thead>
        <tbody>
        @forelse ($stats['districts'] as $row)
            <tr><td>{{ $row['district'] }}</td><td class="num">{{ $row['total'] }}</td><td class="num">{{ $row['average'] !== null ? number_format($row['average'], 2, ',', '.') : '-' }}</td><td class="num">{{ $row['index'] ?? '-' }}</td></tr>
        @empty
            <tr><td colspan="4" class="num">Belum ada data</td></tr>
        @endforelse
        </tbody>
    </table>

    @if ($suggestions->isNotEmpty())
        <h2>D. Saran & Masukan Pelanggan</h2>
        <ul>
            @foreach ($suggestions as $s)<li>{{ $s }}</li>@endforeach
        </ul>
    @endif

    <p style="margin-top:24px;color:#64748b;font-size:11px">Keterangan: Indeks Kepuasan = (skor rata-rata ÷ 5) × 100. Kategori: ≥4,5 Sangat Puas; ≥3,5 Puas; ≥2,5 Cukup Puas; ≥1,5 Tidak Puas; &lt;1,5 Sangat Tidak Puas.</p>

    <div class="sign">
        <div>
            <p>Pangkalpinang, {{ now()->translatedFormat('d F Y') }}</p>
            <p style="margin-bottom:64px">Mengetahui,</p>
            <p><b>{{ auth()->user()->name }}</b><br>{{ auth()->user()->role_label }}</p>
        </div>
    </div>
</div>
</body>
</html>
