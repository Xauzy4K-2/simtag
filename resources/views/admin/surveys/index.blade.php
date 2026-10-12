<x-layout.admin title="Survei">
    <x-page-header title="Manajemen Survei" subtitle="Buat survei, kelola pertanyaan, dan pantau hasilnya.">
        <x-slot:actions>
            <a href="{{ route('admin.surveys.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i>Buat Survei</a>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="no-scrollbar -mx-4 flex gap-2 overflow-x-auto px-4 sm:mx-0 sm:px-0">
            @foreach (['' => 'Semua'] + \App\Models\Survey::STATUSES as $key => $label)
                <a href="{{ route('admin.surveys.index', array_filter(['status' => $key, 'search' => request('search')])) }}"
                   class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition {{ (string) request('status') === (string) $key ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-brand-300' }}">{{ $label }}</a>
            @endforeach
        </div>
        <form method="GET" class="relative sm:w-72">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
            <input name="search" value="{{ request('search') }}" placeholder="Cari judul survei..." class="input bg-white pl-10">
        </form>
    </div>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($surveys as $survey)
            @php $avg = $survey->responses_avg_average_score ? (float) $survey->responses_avg_average_score : null; @endphp
            <article class="card flex flex-col">
                <div class="card-body flex flex-1 flex-col">
                    <div class="flex items-start justify-between gap-3">
                        <x-status-badge :status="$survey->display_status" :label="$survey->status_label" />
                        <span class="text-xs text-slate-400">{{ $survey->created_at->translatedFormat('d M Y') }}</span>
                    </div>
                    <a href="{{ route('admin.surveys.show', $survey) }}" class="mt-3 text-lg font-bold leading-snug text-slate-900 hover:text-brand-700">{{ $survey->title }}</a>
                    <p class="mt-1.5 line-clamp-2 flex-1 text-sm text-slate-500">{{ $survey->description ?: 'Tanpa deskripsi.' }}</p>

                    <div class="mt-5 grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-xl bg-slate-50 p-2.5"><p class="text-lg font-extrabold text-slate-900">{{ $survey->questions_count }}</p><p class="text-[11px] text-slate-500">Pertanyaan</p></div>
                        <div class="rounded-xl bg-slate-50 p-2.5"><p class="text-lg font-extrabold text-slate-900">{{ $survey->responses_count }}</p><p class="text-[11px] text-slate-500">Respons</p></div>
                        <div class="rounded-xl bg-brand-50 p-2.5"><p class="text-lg font-extrabold text-brand-700">{{ $avg ? number_format($avg, 1, ',', '.') : '–' }}</p><p class="text-[11px] text-brand-600">Skor</p></div>
                    </div>
                </div>
                <div class="grid grid-cols-2 border-t border-slate-100">
                    <a href="{{ route('admin.surveys.show', $survey) }}" class="flex items-center justify-center gap-2 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-brand-700"><i class="fa-solid fa-sliders"></i>Kelola</a>
                    <a href="{{ route('admin.surveys.results', $survey) }}" class="flex items-center justify-center gap-2 border-l border-slate-100 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-brand-700"><i class="fa-solid fa-chart-column"></i>Hasil</a>
                </div>
            </article>
        @empty
            <div class="card md:col-span-2 xl:col-span-3">
                <x-empty-state icon="fa-square-poll-vertical" title="Belum ada survei" description="Mulai dengan membuat survei kepuasan pertama Anda.">
                    <a href="{{ route('admin.surveys.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i>Buat Survei</a>
                </x-empty-state>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $surveys->links() }}</div>
</x-layout.admin>
