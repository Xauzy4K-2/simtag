<x-layout.customer title="Survei">
    <x-page-header title="Survei Kepuasan" subtitle="Penilaian Anda membantu PDAM Tirta Pinang meningkatkan kualitas layanan." />

    <section class="mb-10">
        <h2 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-slate-500">
            <i class="fa-solid fa-hourglass-half text-amber-500"></i> Menunggu Diisi
            <span class="badge badge-amber">{{ $available->count() }}</span>
        </h2>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($available as $survey)
                <article class="card flex flex-col p-5">
                    <div class="flex items-start justify-between gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-lg text-amber-500"><i class="fa-solid fa-star-half-stroke"></i></span>
                        <x-status-badge status="published" label="Dibuka" />
                    </div>
                    <h3 class="mt-4 font-bold text-slate-900">{{ $survey->title }}</h3>
                    <p class="mt-1.5 line-clamp-3 flex-1 text-sm text-slate-500">{{ $survey->description }}</p>
                    <div class="mt-4 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                        <span><i class="fa-regular fa-circle-question mr-1"></i>{{ $survey->questions_count }} pertanyaan</span>
                        <span><i class="fa-regular fa-calendar mr-1"></i>{{ $survey->ends_at ? 'Hingga '.$survey->ends_at->translatedFormat('d M Y') : 'Tanpa batas' }}</span>
                    </div>
                    <a href="{{ route('app.surveys.show', $survey) }}" class="btn btn-primary mt-5 w-full">Mulai Isi Survei <i class="fa-solid fa-arrow-right"></i></a>
                </article>
            @empty
                <div class="card md:col-span-2 xl:col-span-3">
                    <x-empty-state icon="fa-mug-hot" title="Tidak ada survei yang menunggu" description="Semua survei aktif sudah Anda isi. Terima kasih atas partisipasinya!" />
                </div>
            @endforelse
        </div>
    </section>

    <section>
        <h2 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-slate-500">
            <i class="fa-solid fa-clipboard-check text-brand-500"></i> Sudah Diisi
            <span class="badge badge-green">{{ $completed->count() }}</span>
        </h2>

        <div class="card divide-y divide-slate-100">
            @forelse ($completed as $survey)
                @php $response = $responses->get($survey->id); @endphp
                <a href="{{ route('app.surveys.show', $survey) }}" class="flex flex-col gap-3 p-4 transition hover:bg-slate-50 sm:flex-row sm:items-center sm:px-6">
                    <div class="flex min-w-0 flex-1 items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><i class="fa-solid fa-check"></i></span>
                        <div class="min-w-0">
                            <p class="truncate font-bold text-slate-800">{{ $survey->title }}</p>
                            <p class="text-xs text-slate-500">Dikirim {{ $response->submitted_at->translatedFormat('d F Y, H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pl-13 sm:pl-0">
                        <x-score-badge :score="$response->average_score" />
                        <i class="fa-solid fa-chevron-right hidden text-slate-300 sm:block"></i>
                    </div>
                </a>
            @empty
                <x-empty-state icon="fa-clipboard-list" title="Belum ada survei yang diisi" />
            @endforelse
        </div>
    </section>
</x-layout.customer>
