<x-layout.admin title="Laporan Kepuasan">
    <x-page-header title="Laporan Kepuasan Pelanggan" subtitle="Analisis gabungan hasil survei berdasarkan periode dan survei.">
        <x-slot:actions>
            <a href="{{ route('admin.reports.export', array_filter($filters)) }}" class="btn btn-primary"><i class="fa-solid fa-file-excel"></i>Ekspor Excel</a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="card mb-6 grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_auto]">
        <div>
            <label class="label" for="survey_id">Survei</label>
            <select id="survey_id" name="survey_id" class="input">
                <option value="">Semua survei</option>
                @foreach ($surveys as $survey)
                    <option value="{{ $survey->id }}" @selected($filters['survey_id'] == $survey->id)>{{ $survey->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label" for="from">Dari tanggal</label>
            <input id="from" type="date" name="from" value="{{ $filters['from'] }}" class="input @error('from') input-error @enderror">
        </div>
        <div>
            <label class="label" for="to">Sampai tanggal</label>
            <input id="to" type="date" name="to" value="{{ $filters['to'] }}" class="input @error('to') input-error @enderror">
            <x-field-error name="to" />
        </div>
        <div class="flex items-end gap-2">
            <button class="btn btn-primary flex-1"><i class="fa-solid fa-filter"></i>Terapkan</button>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
        </div>
    </form>

    <div class="mb-6 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        <div class="card p-4 sm:p-5"><p class="text-xs font-semibold text-slate-500">Total Respons</p><p class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($overall['total'], 0, ',', '.') }}</p></div>
        <div class="card p-4 sm:p-5"><p class="text-xs font-semibold text-slate-500">Responden Unik</p><p class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($overall['respondents'], 0, ',', '.') }}</p></div>
        <div class="card p-4 sm:p-5"><p class="text-xs font-semibold text-slate-500">Indeks Kepuasan</p><p class="mt-1 text-3xl font-extrabold text-brand-700">{{ $overall['index'] !== null ? number_format($overall['index'], 1, ',', '.') : '–' }}</p></div>
        <div class="card p-4 sm:p-5"><p class="text-xs font-semibold text-slate-500">Kategori</p><p class="mt-2"><x-score-badge :score="$overall['average']" class="text-xs" /></p></div>
    </div>

    <div class="mb-6 grid gap-6 lg:grid-cols-3">
        <section class="card card-body lg:col-span-2">
            <h2 class="card-title">Tren 12 Bulan Terakhir</h2>
            <div class="mt-4 h-72">
                <canvas data-chart="{{ json_encode([
                    'type' => 'line',
                    'data' => ['labels' => $trend->pluck('label'), 'datasets' => [
                        ['label' => 'Skor rata-rata', 'data' => $trend->pluck('average'), 'borderColor' => '#059669', 'backgroundColor' => 'rgba(16,185,129,.12)', 'fill' => true, 'tension' => 0.35, 'spanGaps' => true, 'yAxisID' => 'y'],
                        ['type' => 'bar', 'label' => 'Jumlah respons', 'data' => $trend->pluck('total'), 'backgroundColor' => '#bae6fd', 'borderRadius' => 6, 'yAxisID' => 'y1'],
                    ]],
                    'options' => [
                        'interaction' => ['mode' => 'index', 'intersect' => false],
                        'plugins' => ['legend' => ['position' => 'bottom']],
                        'scales' => ['x' => ['grid' => ['display' => false]], 'y' => ['min' => 0, 'max' => 5], 'y1' => ['position' => 'right', 'beginAtZero' => true, 'grid' => ['display' => false], 'ticks' => ['precision' => 0]]],
                    ],
                ]) }}"></canvas>
            </div>
        </section>
        <section class="card card-body">
            <h2 class="card-title">Sebaran Kategori</h2>
            <div class="mt-4 h-72">
                <canvas data-chart="{{ json_encode([
                    'type' => 'doughnut',
                    'data' => ['labels' => $overall['categories']->keys(), 'datasets' => [[
                        'data' => $overall['categories']->values(),
                        'backgroundColor' => ['#10b981', '#0ea5e9', '#fbbf24', '#f97316', '#f43f5e'],
                        'borderWidth' => 2, 'borderColor' => '#fff',
                    ]]],
                    'options' => ['cutout' => '62%', 'plugins' => ['legend' => ['position' => 'bottom']]],
                ]) }}"></canvas>
            </div>
        </section>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="card">
            <div class="card-header"><h2 class="card-title">Kepuasan per Kecamatan</h2></div>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Kecamatan</th><th class="text-right">Respons</th><th class="text-right">Skor</th><th class="w-2/5">Indeks</th></tr></thead>
                    <tbody>
                        @forelse ($districts as $row)
                            <tr>
                                <td class="font-semibold text-slate-800">{{ $row['district'] }}</td>
                                <td class="text-right">{{ $row['total'] }}</td>
                                <td class="text-right">{{ $row['average'] !== null ? number_format($row['average'], 2, ',', '.') : '–' }}</td>
                                <td><div class="flex items-center gap-2"><div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-brand-500" style="width: {{ $row['index'] ?? 0 }}%"></div></div><span class="w-10 text-right text-xs font-bold">{{ $row['index'] ?? '–' }}</span></div></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-slate-400">Tidak ada data pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card">
            <div class="card-header"><div><h2 class="card-title">Peringkat Aspek Layanan</h2><p class="text-xs text-slate-500">Diurutkan dari skor terendah (prioritas perbaikan)</p></div></div>
            <div class="divide-y divide-slate-100">
                @forelse ($weakest as $i => $row)
                    <div class="flex items-center gap-3 px-4 py-3 sm:px-6">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-xs font-extrabold {{ $row['average'] < 3.5 ? 'bg-rose-50 text-rose-600' : 'bg-slate-100 text-slate-600' }}">{{ $i + 1 }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-800">{{ $row['question'] }}</p>
                            <p class="truncate text-xs text-slate-400">{{ $row['survey'] }}</p>
                        </div>
                        <div class="text-right"><p class="font-extrabold text-slate-900">{{ number_format($row['average'], 2, ',', '.') }}</p><p class="text-[11px] text-slate-400">{{ $row['total'] }} jwb</p></div>
                    </div>
                @empty
                    <x-empty-state icon="fa-chart-simple" title="Belum ada penilaian" />
                @endforelse
            </div>
        </section>
    </div>
</x-layout.admin>
