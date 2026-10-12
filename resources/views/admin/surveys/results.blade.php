<x-layout.admin :title="'Hasil · '.$survey->title">
    <x-page-header :title="$survey->title" eyebrow="Hasil Survei" :back="route('admin.surveys.show', $survey)">
        <x-slot:actions>
            <a href="{{ route('admin.surveys.print', $survey) }}" target="_blank" class="btn btn-secondary"><i class="fa-solid fa-print"></i>Cetak / PDF</a>
            <a href="{{ route('admin.surveys.export', $survey) }}" class="btn btn-primary"><i class="fa-solid fa-file-excel"></i>Ekspor Excel</a>
        </x-slot:actions>
    </x-page-header>

    {{-- Ringkasan --}}
    <div class="mb-6 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        <div class="card p-4 sm:p-5"><p class="text-xs font-semibold text-slate-500">Total Responden</p><p class="mt-1 text-3xl font-extrabold text-slate-900">{{ $stats['total'] }}</p></div>
        <div class="card p-4 sm:p-5"><p class="text-xs font-semibold text-slate-500">Skor Rata-rata</p><p class="mt-1 text-3xl font-extrabold text-slate-900">{{ $stats['average'] !== null ? number_format($stats['average'], 2, ',', '.') : '–' }}<span class="text-base text-slate-400">/5</span></p></div>
        <div class="card p-4 sm:p-5"><p class="text-xs font-semibold text-slate-500">Indeks Kepuasan</p><p class="mt-1 text-3xl font-extrabold text-brand-700">{{ $stats['index'] !== null ? number_format($stats['index'], 1, ',', '.') : '–' }}</p></div>
        <div class="card p-4 sm:p-5"><p class="text-xs font-semibold text-slate-500">Kategori</p><p class="mt-2"><x-score-badge :score="$stats['average']" class="text-xs" /></p></div>
    </div>

    @if ($stats['total'] === 0)
        <div class="card">
            <x-empty-state icon="fa-chart-column" title="Belum ada responden" description="Hasil akan tampil setelah pelanggan mulai mengisi survei ini." />
        </div>
    @else
        <div class="mb-6 grid gap-6 lg:grid-cols-3">
            <section class="card card-body lg:col-span-2">
                <h2 class="card-title">Perkembangan Respons Harian</h2>
                <div class="mt-4 h-56">
                    <canvas data-chart="{{ json_encode([
                        'type' => 'line',
                        'data' => ['labels' => $stats['timeline']->pluck('label'), 'datasets' => [[
                            'label' => 'Respons', 'data' => $stats['timeline']->pluck('total'), 'borderColor' => '#059669',
                            'backgroundColor' => 'rgba(16,185,129,.12)', 'fill' => true, 'tension' => 0.35, 'pointRadius' => 3,
                        ]]],
                        'options' => ['plugins' => ['legend' => ['display' => false]], 'scales' => ['x' => ['grid' => ['display' => false]], 'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]]],
                    ]) }}"></canvas>
                </div>
            </section>
            <section class="card">
                <div class="card-header"><h2 class="card-title">Per Kecamatan</h2></div>
                <div class="divide-y divide-slate-100">
                    @foreach ($stats['districts'] as $row)
                        <div class="px-4 py-3 sm:px-6">
                            <div class="flex justify-between text-sm"><span class="font-semibold text-slate-700">{{ $row['district'] }}</span><span class="text-xs text-slate-500">{{ $row['total'] }} resp · <b class="text-slate-800">{{ $row['index'] ?? '–' }}</b></span></div>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-brand-500" style="width: {{ $row['index'] ?? 0 }}%"></div></div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        {{-- Hasil per pertanyaan --}}
        <h2 class="mb-4 text-lg font-extrabold text-slate-900">Hasil per Pertanyaan</h2>
        <div class="mb-8 grid gap-4 lg:grid-cols-2">
            @foreach ($stats['questions'] as $i => $q)
                @php $question = $q['model']; @endphp
                <section class="card card-body">
                    <div class="flex items-start gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-sm font-extrabold text-brand-700">{{ $i + 1 }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-800">{{ $question->question_text }}</p>
                            <p class="mt-0.5 text-xs text-slate-400">{{ $question->type_label }} · {{ $q['answered'] }} jawaban</p>
                        </div>
                    </div>

                    @if ($question->question_type === 'rating')
                        <div class="mt-4 flex items-center gap-4">
                            <div class="text-center">
                                <p class="text-3xl font-extrabold text-slate-900">{{ $q['average'] !== null ? number_format($q['average'], 2, ',', '.') : '–' }}</p>
                                <x-stars :value="$q['average'] ?? 0" size="text-xs" />
                                <p class="mt-1 text-[11px] font-semibold text-slate-500">{{ $q['label'] }}</p>
                            </div>
                            @php $max = max(1, $q['distribution']->max()); @endphp
                            <div class="flex-1 space-y-1.5">
                                @foreach ($q['distribution']->sortKeysDesc() as $star => $count)
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="w-6 font-semibold text-slate-500">{{ $star }}<i class="fa-solid fa-star ml-0.5 text-[9px] text-amber-400"></i></span>
                                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full {{ $star >= 4 ? 'bg-brand-500' : ($star == 3 ? 'bg-amber-400' : 'bg-rose-400') }}" style="width: {{ $count / $max * 100 }}%"></div></div>
                                        <span class="w-8 text-right text-slate-600">{{ $count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif ($question->question_type === 'multiple_choice')
                        @php $sum = max(1, $q['options']->sum()); @endphp
                        <div class="mt-4 space-y-2.5">
                            @foreach ($q['options']->sortDesc() as $option => $count)
                                <div>
                                    <div class="mb-1 flex justify-between gap-3 text-sm"><span class="text-slate-700">{{ $option }}</span><span class="shrink-0 font-semibold text-slate-800">{{ $count }} <span class="text-xs font-normal text-slate-400">({{ round($count / $sum * 100) }}%)</span></span></div>
                                    <div class="h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-violet-500" style="width: {{ $count / $sum * 100 }}%"></div></div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-4 max-h-60 space-y-2 overflow-y-auto pr-1">
                            @forelse ($q['texts'] as $text)
                                <p class="rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-700">“{{ $text }}”</p>
                            @empty
                                <p class="text-sm italic text-slate-400">Belum ada jawaban.</p>
                            @endforelse
                        </div>
                    @endif
                </section>
            @endforeach
        </div>

        @if ($suggestions->isNotEmpty())
            <section class="card mb-8">
                <div class="card-header"><h2 class="card-title"><i class="fa-solid fa-comment-medical mr-1.5 text-amber-500"></i>Saran & Masukan Terbaru</h2></div>
                <div class="grid gap-3 p-4 sm:grid-cols-2 sm:p-6">
                    @foreach ($suggestions as $item)
                        <blockquote class="rounded-2xl border border-amber-100 bg-amber-50/60 p-4">
                            <p class="text-sm text-slate-700">“{{ $item->suggestion }}”</p>
                            <footer class="mt-2 text-xs text-slate-500">— {{ $item->customer?->full_name }}, {{ $item->submitted_at->translatedFormat('d M Y') }}</footer>
                        </blockquote>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Daftar responden --}}
        <section class="card" id="responden">
            <div class="card-header">
                <h2 class="card-title">Daftar Responden</h2>
                <form method="GET" action="{{ route('admin.surveys.results', $survey) }}#responden" class="relative w-full sm:w-64">
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                    <input name="search" value="{{ request('search') }}" placeholder="Cari responden..." class="input pl-10">
                </form>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Responden</th><th class="hidden sm:table-cell">Kecamatan</th><th>Skor</th><th class="hidden md:table-cell">Waktu</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($responses as $response)
                            <tr>
                                <td>
                                    <p class="font-bold text-slate-800">{{ $response->customer?->full_name }}</p>
                                    <p class="font-mono text-xs text-slate-400">{{ $response->customer?->customer_number }}</p>
                                </td>
                                <td class="hidden sm:table-cell">{{ $response->customer?->district ?? '-' }}</td>
                                <td><x-score-badge :score="$response->average_score" /></td>
                                <td class="hidden whitespace-nowrap text-xs text-slate-500 md:table-cell">{{ $response->submitted_at->translatedFormat('d M Y, H:i') }}</td>
                                <td class="text-right"><a href="{{ route('admin.surveys.responses.show', [$survey, $response]) }}" class="btn btn-ghost btn-sm"><i class="fa-solid fa-eye"></i><span class="hidden sm:inline">Detail</span></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-8 text-center text-slate-400">Responden tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-4 py-3 sm:px-6">{{ $responses->fragment('responden')->links() }}</div>
        </section>
    @endif
</x-layout.admin>
