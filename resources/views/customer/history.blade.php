<x-layout.customer title="Riwayat">
    <x-page-header title="Riwayat Aktivitas" :subtitle="$total.' aktivitas tercatat'" />

    <div class="no-scrollbar -mx-4 mb-6 flex gap-2 overflow-x-auto px-4 sm:mx-0 sm:px-0">
        @foreach (['' => 'Semua', 'survey' => 'Survei', 'complaint' => 'Pengaduan'] as $key => $label)
            <a href="{{ route('app.history', array_filter(['type' => $key])) }}"
               class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition {{ (string) $type === (string) $key ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-brand-300' }}">{{ $label }}</a>
        @endforeach
    </div>

    @forelse ($groups as $month => $items)
        <section class="mb-8">
            <h2 class="mb-3 text-xs font-bold uppercase tracking-widest text-slate-400">{{ $month }}</h2>
            <ol class="relative space-y-3 border-l-2 border-slate-200 pl-6">
                @foreach ($items as $item)
                    @php $isSurvey = $item['type'] === 'survey'; @endphp
                    <li class="relative">
                        <span class="absolute -left-[2.15rem] top-4 flex h-6 w-6 items-center justify-center rounded-full text-[10px] ring-4 ring-slate-50 {{ $isSurvey ? 'bg-brand-500 text-white' : 'bg-rose-500 text-white' }}">
                            <i class="fa-solid {{ $isSurvey ? 'fa-star' : 'fa-comment' }}"></i>
                        </span>
                        <a href="{{ $item['url'] }}" class="card flex flex-col gap-2 p-4 transition hover:shadow-md sm:flex-row sm:items-center">
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-bold uppercase tracking-wide {{ $isSurvey ? 'text-brand-600' : 'text-rose-500' }}">{{ $isSurvey ? 'Survei' : 'Pengaduan' }}</p>
                                <p class="truncate font-bold text-slate-800">{{ $item['title'] }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $item['description'] }}</p>
                            </div>
                            <div class="flex items-center justify-between gap-3 sm:flex-col sm:items-end">
                                @if ($isSurvey)
                                    <span class="badge badge-green">{{ $item['badge'] }}</span>
                                @else
                                    <x-status-badge :status="$item['status']" />
                                @endif
                                <span class="text-xs text-slate-400">{{ $item['date']->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ol>
        </section>
    @empty
        <div class="card">
            <x-empty-state icon="fa-clock-rotate-left" title="Belum ada riwayat" description="Aktivitas survei dan pengaduan Anda akan tercatat di sini." />
        </div>
    @endforelse
</x-layout.customer>
