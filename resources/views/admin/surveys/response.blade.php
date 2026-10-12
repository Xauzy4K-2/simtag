<x-layout.admin title="Detail Jawaban">
    <x-page-header :title="$response->customer?->full_name ?? 'Responden'" :eyebrow="$survey->title" :back="route('admin.surveys.results', $survey)">
        <x-slot:actions>
            @if ($response->customer)
                <a href="{{ route('admin.customers.show', $response->customer) }}" class="btn btn-secondary"><i class="fa-solid fa-user"></i>Profil Pelanggan</a>
            @endif
            <form method="POST" action="{{ route('admin.surveys.responses.destroy', [$survey, $response]) }}" data-confirm="Hapus jawaban responden ini? Pelanggan akan dapat mengisi ulang survei.">
                @csrf @method('DELETE')
                <button class="btn btn-soft-danger"><i class="fa-solid fa-trash"></i>Hapus Jawaban</button>
            </form>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="card p-4"><p class="text-xs font-semibold text-slate-500">No. Pelanggan</p><p class="mt-1 font-mono font-bold text-slate-900">{{ $response->customer?->customer_number }}</p><p class="text-xs text-slate-500">{{ $response->customer?->district }}</p></div>
        <div class="card p-4"><p class="text-xs font-semibold text-slate-500">Waktu Pengisian</p><p class="mt-1 font-bold text-slate-900">{{ $response->submitted_at->translatedFormat('d F Y') }}</p><p class="text-xs text-slate-500">Pukul {{ $response->submitted_at->format('H:i') }}</p></div>
        <div class="card p-4"><p class="text-xs font-semibold text-slate-500">Skor Rata-rata</p><p class="mt-1"><x-score-badge :score="$response->average_score" class="text-xs" /></p></div>
    </div>

    <div class="card divide-y divide-slate-100">
        @foreach ($survey->questions as $i => $question)
            @php $answer = $answers->get($question->id); @endphp
            <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-start sm:p-6">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-sm font-extrabold text-slate-600">{{ $i + 1 }}</span>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-slate-800">{{ $question->question_text }}</p>
                    <div class="mt-2">
                        @if (! $answer)
                            <span class="text-sm italic text-slate-400">Tidak dijawab</span>
                        @elseif ($question->question_type === 'rating')
                            <span class="inline-flex flex-wrap items-center gap-2"><x-stars :value="$answer->answer_value" /><span class="text-sm font-semibold text-slate-600">{{ $answer->answer_value }} · {{ \App\Models\SurveyQuestion::RATING_LABELS[$answer->answer_value] ?? '' }}</span></span>
                        @else
                            <p class="rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-700">{{ $answer->answer_text }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        <div class="p-4 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Saran & Masukan</p>
            <p class="mt-2 text-sm {{ $response->suggestion ? 'rounded-xl bg-amber-50 px-3 py-2 text-amber-900' : 'italic text-slate-400' }}">{{ $response->suggestion ?: 'Tidak ada saran.' }}</p>
        </div>
    </div>
</x-layout.admin>
