<x-layout.admin title="Ringkasan">
    <x-page-header eyebrow="Pusat Kontrol" :title="'Selamat datang, '.auth()->user()->name" subtitle="Ringkasan kepuasan pelanggan dan pengaduan layanan PDAM Tirta Pinang.">
        <x-slot:actions>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary"><i class="fa-solid fa-file-lines"></i>Laporan</a>
            <a href="{{ route('admin.surveys.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i>Buat Survei</a>
        </x-slot:actions>
    </x-page-header>

    {{-- Kartu statistik --}}
    <div class="mb-6 grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
        @foreach ([
            ['Pelanggan', number_format($stats['customers'], 0, ',', '.'), $stats['activeCustomers'].' aktif', 'fa-users', 'bg-sky-50 text-sky-600', route('admin.customers.index')],
            ['Survei Aktif', $stats['activeSurveys'], 'dari '.$stats['totalSurveys'].' survei', 'fa-square-poll-vertical', 'bg-violet-50 text-violet-600', route('admin.surveys.index')],
            ['Total Respons', number_format($stats['responses'], 0, ',', '.'), $stats['responsesThisMonth'].' bulan ini', 'fa-clipboard-check', 'bg-brand-50 text-brand-600', route('admin.reports.index')],
            ['Pengaduan Aktif', $stats['openComplaints'], $stats['newComplaints'].' belum ditangani', 'fa-triangle-exclamation', 'bg-amber-50 text-amber-600', route('admin.complaints.index')],
        ] as [$label, $value, $meta, $icon, $color, $url])
            <a href="{{ $url }}" class="card p-4 transition hover:-translate-y-0.5 hover:shadow-md sm:p-5">
                <div class="flex items-start justify-between gap-2">
                    <span class="stat-icon {{ $color }}"><i class="fa-solid {{ $icon }}"></i></span>
                    @if ($label === 'Total Respons' && $stats['responsesGrowth'] !== null)
                        <span class="badge {{ $stats['responsesGrowth'] >= 0 ? 'badge-green' : 'badge-red' }}">
                            <i class="fa-solid {{ $stats['responsesGrowth'] >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>{{ abs($stats['responsesGrowth']) }}%
                        </span>
                    @endif
                </div>
                <p class="mt-4 text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ $value }}</p>
                <p class="text-sm font-semibold text-slate-600">{{ $label }}</p>
                <p class="mt-0.5 text-xs text-slate-400">{{ $meta }}</p>
            </a>
        @endforeach
    </div>

    <div class="mb-6 grid gap-6 lg:grid-cols-3">
        {{-- Indeks kepuasan --}}
        <section class="card card-body flex flex-col">
            <h2 class="card-title">Indeks Kepuasan Pelanggan</h2>
            <p class="text-xs text-slate-500">Rata-rata seluruh survei (skala 0–100)</p>
            <div class="relative mx-auto my-4 h-48 w-48">
                <canvas data-chart="{{ json_encode([
                    'type' => 'doughnut',
                    'data' => ['datasets' => [[
                        'data' => [$overall['index'] ?? 0, 100 - ($overall['index'] ?? 0)],
                        'backgroundColor' => ['#059669', '#e2e8f0'],
                        'borderWidth' => 0,
                        'borderRadius' => 8,
                    ]]],
                    'options' => ['cutout' => '78%', 'rotation' => -120, 'circumference' => 240, 'plugins' => ['tooltip' => ['enabled' => false], 'legend' => ['display' => false]]],
                ]) }}"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pt-2">
                    <p class="text-4xl font-extrabold text-slate-900">{{ $overall['index'] !== null ? number_format($overall['index'], 1, ',', '.') : '–' }}</p>
                    <p class="text-xs font-semibold text-slate-500">dari 100</p>
                </div>
            </div>
            <div class="mt-auto grid grid-cols-2 gap-3 text-center">
                <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Skor rata-rata</p><p class="font-extrabold text-slate-900">{{ $overall['average'] !== null ? number_format($overall['average'], 2, ',', '.') : '–' }} / 5</p></div>
                <div class="rounded-xl bg-brand-50 p-3"><p class="text-xs text-brand-700">Kategori</p><p class="font-extrabold text-brand-800">{{ $overall['label'] }}</p></div>
            </div>
        </section>

        {{-- Tren --}}
        <section class="card card-body lg:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="card-title">Tren Respons & Skor</h2>
                    <p class="text-xs text-slate-500">6 bulan terakhir</p>
                </div>
            </div>
            <div class="mt-4 h-64 sm:h-72">
                <canvas data-chart="{{ json_encode([
                    'type' => 'bar',
                    'data' => [
                        'labels' => $trend->pluck('label'),
                        'datasets' => [
                            ['type' => 'bar', 'label' => 'Jumlah respons', 'data' => $trend->pluck('total'), 'backgroundColor' => '#a7f3d0', 'hoverBackgroundColor' => '#34d399', 'borderRadius' => 8, 'yAxisID' => 'y', 'order' => 2],
                            ['type' => 'line', 'label' => 'Skor rata-rata', 'data' => $trend->pluck('average'), 'borderColor' => '#0284c7', 'backgroundColor' => '#0284c7', 'tension' => 0.35, 'pointRadius' => 4, 'spanGaps' => true, 'yAxisID' => 'y1', 'order' => 1],
                        ],
                    ],
                    'options' => [
                        'interaction' => ['mode' => 'index', 'intersect' => false],
                        'plugins' => ['legend' => ['position' => 'bottom']],
                        'scales' => [
                            'x' => ['grid' => ['display' => false]],
                            'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0], 'grid' => ['color' => '#f1f5f9']],
                            'y1' => ['position' => 'right', 'min' => 0, 'max' => 5, 'grid' => ['display' => false]],
                        ],
                    ],
                ]) }}"></canvas>
            </div>
        </section>
    </div>

    <div class="mb-6 grid gap-6 lg:grid-cols-2">
        {{-- Sebaran kategori --}}
        <section class="card card-body">
            <h2 class="card-title">Sebaran Tingkat Kepuasan</h2>
            <p class="text-xs text-slate-500">Berdasarkan skor rata-rata tiap responden</p>
            @php $totalCat = max(1, $overall['categories']->sum()); @endphp
            <div class="mt-5 space-y-3.5">
                @foreach ($overall['categories'] as $label => $count)
                    @php $color = ['Sangat Puas' => 'bg-emerald-500', 'Puas' => 'bg-sky-500', 'Cukup Puas' => 'bg-amber-400', 'Tidak Puas' => 'bg-orange-500', 'Sangat Tidak Puas' => 'bg-rose-500'][$label] ?? 'bg-slate-400'; @endphp
                    <div>
                        <div class="mb-1 flex justify-between text-sm"><span class="font-semibold text-slate-700">{{ $label }}</span><span class="text-slate-500">{{ $count }} <span class="text-xs">({{ round($count / $totalCat * 100) }}%)</span></span></div>
                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full {{ $color }}" style="width: {{ $count / $totalCat * 100 }}%"></div></div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Per kecamatan --}}
        <section class="card">
            <div class="card-header">
                <div><h2 class="card-title">Kepuasan per Kecamatan</h2><p class="text-xs text-slate-500">Indeks kepuasan & jumlah respons</p></div>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Kecamatan</th><th class="text-right">Respons</th><th class="w-1/2">Indeks</th></tr></thead>
                    <tbody>
                        @forelse ($districts as $row)
                            <tr>
                                <td class="font-semibold text-slate-800">{{ $row['district'] }}</td>
                                <td class="text-right">{{ $row['total'] }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-brand-500" style="width: {{ $row['index'] ?? 0 }}%"></div></div>
                                        <span class="w-10 text-right text-xs font-bold text-slate-700">{{ $row['index'] ?? '–' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-8 text-center text-slate-400">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="mb-6 grid gap-6 lg:grid-cols-2">
        {{-- Aspek terlemah --}}
        <section class="card card-body">
            <h2 class="card-title"><i class="fa-solid fa-arrow-trend-down mr-1.5 text-rose-500"></i>Aspek Prioritas Perbaikan</h2>
            <p class="text-xs text-slate-500">Pertanyaan dengan skor rata-rata terendah</p>
            <ol class="mt-4 space-y-3">
                @forelse ($weakest as $i => $row)
                    <li class="flex items-start gap-3 rounded-xl border border-slate-100 p-3">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-xs font-extrabold text-rose-600">{{ $i + 1 }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-800">{{ $row['question'] }}</p>
                            <p class="truncate text-xs text-slate-400">{{ $row['survey'] }} · {{ $row['total'] }} jawaban</p>
                        </div>
                        <span class="shrink-0 text-right"><span class="block text-lg font-extrabold text-slate-900">{{ number_format($row['average'], 2, ',', '.') }}</span><x-stars :value="$row['average']" size="text-[9px]" /></span>
                    </li>
                @empty
                    <x-empty-state icon="fa-chart-simple" title="Belum ada data penilaian" />
                @endforelse
            </ol>
        </section>

        {{-- Survei aktif --}}
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Survei Sedang Berjalan</h2>
                <a href="{{ route('admin.surveys.index') }}" class="text-sm font-semibold text-brand-600 hover:underline">Kelola</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($surveys as $survey)
                    <a href="{{ route('admin.surveys.results', $survey) }}" class="flex items-center gap-3 px-4 py-3.5 transition hover:bg-slate-50 sm:px-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600"><i class="fa-solid fa-square-poll-vertical"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $survey->title }}</p>
                            <p class="text-xs text-slate-500">{{ $survey->ends_at ? 'Berakhir '.$survey->ends_at->translatedFormat('d M Y') : 'Tanpa batas waktu' }}</p>
                        </div>
                        <div class="text-right"><p class="font-extrabold text-slate-900">{{ $survey->responses_count }}</p><p class="text-[11px] text-slate-400">respons</p></div>
                    </a>
                @empty
                    <x-empty-state icon="fa-square-poll-vertical" title="Tidak ada survei aktif">
                        <a href="{{ route('admin.surveys.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i>Buat Survei</a>
                    </x-empty-state>
                @endforelse
            </div>
        </section>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="card">
            <div class="card-header"><h2 class="card-title">Respons Terbaru</h2></div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentResponses as $response)
                    <a href="{{ route('admin.surveys.responses.show', [$response->survey_id, $response]) }}" class="flex items-center gap-3 px-4 py-3 transition hover:bg-slate-50 sm:px-6">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700">{{ $response->customer?->initials }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $response->customer?->full_name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $response->survey?->title }} · {{ $response->submitted_at->diffForHumans() }}</p>
                        </div>
                        <x-score-badge :score="$response->average_score" class="hidden sm:inline-flex" />
                    </a>
                @empty
                    <x-empty-state icon="fa-inbox" title="Belum ada respons" />
                @endforelse
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Pengaduan Terbaru</h2>
                <a href="{{ route('admin.complaints.index') }}" class="text-sm font-semibold text-brand-600 hover:underline">Lihat semua</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentComplaints as $complaint)
                    <a href="{{ route('admin.complaints.show', $complaint) }}" class="flex items-center gap-3 px-4 py-3 transition hover:bg-slate-50 sm:px-6">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $complaint->subject }}</p>
                            <p class="truncate text-xs text-slate-500"><span class="font-mono">{{ $complaint->ticket_number }}</span> · {{ $complaint->customer?->full_name }} · {{ $complaint->created_at->diffForHumans() }}</p>
                        </div>
                        <x-status-badge :status="$complaint->status" />
                    </a>
                @empty
                    <x-empty-state icon="fa-inbox" title="Belum ada pengaduan" />
                @endforelse
            </div>
        </section>
    </div>
</x-layout.admin>
