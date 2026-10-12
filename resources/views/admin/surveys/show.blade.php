<x-layout.admin :title="$survey->title">
    <x-page-header :title="$survey->title" eyebrow="Kelola Survei" :back="route('admin.surveys.index')">
        <x-slot:actions>
            <a href="{{ route('admin.surveys.results', $survey) }}" class="btn btn-secondary"><i class="fa-solid fa-chart-column"></i>Hasil ({{ $survey->responses_count }})</a>
            @if ($survey->status !== 'published')
                <form method="POST" action="{{ route('admin.surveys.publish', $survey) }}" data-confirm="Publikasikan survei ini agar dapat diisi pelanggan?">
                    @csrf<button class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i>{{ $survey->status === 'closed' ? 'Buka Kembali' : 'Publikasikan' }}</button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.surveys.close', $survey) }}" data-confirm="Tutup survei ini? Pelanggan tidak dapat mengisi lagi.">
                    @csrf<button class="btn btn-secondary text-amber-700"><i class="fa-solid fa-lock"></i>Tutup Survei</button>
                </form>
            @endif
            <div x-data="{ open: false }" class="relative">
                <button type="button" @click="open = !open" @click.outside="open = false" class="btn btn-secondary" aria-label="Aksi lainnya"><i class="fa-solid fa-ellipsis"></i></button>
                <div x-cloak x-show="open" x-transition class="absolute right-0 z-20 mt-2 w-52 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-xl">
                    <a href="{{ route('admin.surveys.edit', $survey) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50"><i class="fa-solid fa-pen w-4"></i>Ubah informasi</a>
                    <form method="POST" action="{{ route('admin.surveys.duplicate', $survey) }}">@csrf<button class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-50"><i class="fa-solid fa-copy w-4"></i>Duplikat survei</button></form>
                    <form method="POST" action="{{ route('admin.surveys.destroy', $survey) }}" data-confirm="Hapus survei beserta {{ $survey->responses_count }} jawaban responden? Tindakan ini tidak dapat dibatalkan.">
                        @csrf @method('DELETE')<button class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm text-rose-600 hover:bg-rose-50"><i class="fa-solid fa-trash w-4"></i>Hapus survei</button>
                    </form>
                </div>
            </div>
        </x-slot:actions>
    </x-page-header>

    {{-- Informasi survei --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card p-4"><p class="text-xs font-semibold text-slate-500">Status</p><div class="mt-2"><x-status-badge :status="$survey->display_status" :label="$survey->status_label" /></div></div>
        <div class="card p-4"><p class="text-xs font-semibold text-slate-500">Pertanyaan</p><p class="mt-1 text-2xl font-extrabold text-slate-900">{{ $survey->questions->count() }}</p></div>
        <div class="card p-4"><p class="text-xs font-semibold text-slate-500">Respons</p><p class="mt-1 text-2xl font-extrabold text-slate-900">{{ $survey->responses_count }}</p></div>
        <div class="card p-4"><p class="text-xs font-semibold text-slate-500">Batas akhir</p><p class="mt-1 font-bold text-slate-900">{{ $survey->ends_at?->translatedFormat('d F Y') ?? 'Tanpa batas' }}</p></div>
    </div>

    @if ($survey->description)
        <div class="card card-body mb-6">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Deskripsi</p>
            <p class="mt-2 text-sm text-slate-700">{{ $survey->description }}</p>
            <p class="mt-3 text-xs text-slate-400">Dibuat oleh {{ $survey->creator?->name ?? '-' }} · {{ $survey->created_at->translatedFormat('d M Y') }}@if ($survey->published_at) · Dipublikasikan {{ $survey->published_at->translatedFormat('d M Y') }}@endif</p>
        </div>
    @endif

    @if ($survey->responses_count > 0)
        <div class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>Survei ini sudah memiliki jawaban. Perubahan teks pertanyaan akan memengaruhi interpretasi hasil, dan tipe pertanyaan yang sudah dijawab tidak dapat diubah.
        </div>
    @endif

    {{-- Daftar pertanyaan --}}
    <section id="pertanyaan" class="scroll-mt-24">
        <h2 class="mb-4 text-lg font-extrabold text-slate-900">Daftar Pertanyaan</h2>

        <div class="space-y-3">
            @forelse ($survey->questions as $i => $question)
                <article id="q-{{ $question->id }}" class="card scroll-mt-24" x-data="{ edit: false }">
                    <div class="flex gap-3 p-4 sm:p-5">
                        <div class="flex flex-col items-center gap-1">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-sm font-extrabold text-brand-700">{{ $i + 1 }}</span>
                            @unless ($loop->first)
                                <form method="POST" action="{{ route('admin.surveys.questions.move', [$survey, $question, 'up']) }}">@csrf<button class="rounded p-1 text-xs text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Naikkan"><i class="fa-solid fa-chevron-up"></i></button></form>
                            @endunless
                            @unless ($loop->last)
                                <form method="POST" action="{{ route('admin.surveys.questions.move', [$survey, $question, 'down']) }}">@csrf<button class="rounded p-1 text-xs text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Turunkan"><i class="fa-solid fa-chevron-down"></i></button></form>
                            @endunless
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-800">{{ $question->question_text }}</p>
                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                <span class="badge {{ ['rating' => 'badge-amber', 'multiple_choice' => 'badge-violet', 'text' => 'badge-blue'][$question->question_type] ?? 'badge-slate' }}">{{ $question->type_label }}</span>
                                <span class="badge {{ $question->is_required ? 'badge-red' : 'badge-slate' }}">{{ $question->is_required ? 'Wajib' : 'Opsional' }}</span>
                                <span class="text-xs text-slate-400">{{ $question->answers_count }} jawaban</span>
                            </div>
                            @if ($question->question_type === 'multiple_choice')
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach ($question->options ?? [] as $option)
                                        <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs text-slate-600"><i class="fa-regular fa-circle mr-1 text-[9px]"></i>{{ $option }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="flex shrink-0 flex-col gap-1 sm:flex-row">
                            <button type="button" @click="edit = !edit" class="btn btn-ghost btn-sm" title="Ubah"><i class="fa-solid" :class="edit ? 'fa-xmark' : 'fa-pen'"></i></button>
                            <form method="POST" action="{{ route('admin.surveys.questions.destroy', [$survey, $question]) }}" data-confirm="Hapus pertanyaan ini{{ $question->answers_count ? ' beserta '.$question->answers_count.' jawabannya' : '' }}?">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-sm text-rose-500 hover:bg-rose-50" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <div x-cloak x-show="edit" x-collapse>
                        <form method="POST" action="{{ route('admin.surveys.questions.update', [$survey, $question]) }}" class="border-t border-slate-100 bg-slate-50/60 p-4 sm:p-5">
                            @csrf @method('PUT')
                            @include('admin.surveys._question-fields', ['question' => $question, 'prefix' => 'q'.$question->id])
                            <div class="mt-4 flex justify-end gap-2">
                                <button type="button" @click="edit = false" class="btn btn-secondary btn-sm">Batal</button>
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-floppy-disk"></i>Simpan</button>
                            </div>
                        </form>
                    </div>
                </article>
            @empty
                <div class="card">
                    <x-empty-state icon="fa-list-ol" title="Belum ada pertanyaan" description="Tambahkan pertanyaan pertama menggunakan form di bawah." />
                </div>
            @endforelse
        </div>

        {{-- Tambah pertanyaan --}}
        <form method="POST" action="{{ route('admin.surveys.questions.store', $survey) }}" class="card mt-6 border-2 border-dashed border-brand-200 bg-brand-50/30">
            @csrf
            <div class="card-header border-brand-100"><h3 class="card-title"><i class="fa-solid fa-circle-plus mr-1.5 text-brand-600"></i>Tambah Pertanyaan</h3></div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        @foreach ($errors->all() as $error)<p><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $error }}</p>@endforeach
                    </div>
                @endif
                @include('admin.surveys._question-fields', ['question' => null, 'prefix' => 'new'])
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="btn btn-primary" data-loading="Menyimpan..."><i class="fa-solid fa-plus"></i>Tambahkan Pertanyaan</button>
                </div>
            </div>
        </form>
    </section>
</x-layout.admin>
