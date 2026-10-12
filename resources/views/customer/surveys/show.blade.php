<x-layout.customer :title="$survey->title">
    <x-page-header :title="$survey->title" :subtitle="$survey->description" :back="route('app.surveys.index')" eyebrow="Survei Kepuasan" />

    @if ($response)
        {{-- ============ Jawaban yang sudah dikirim ============ --}}
        <div class="mb-6 flex flex-col gap-4 rounded-3xl border border-brand-100 bg-brand-50 p-5 sm:flex-row sm:items-center">
            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white text-2xl text-brand-600"><i class="fa-solid fa-circle-check"></i></span>
            <div class="flex-1">
                <p class="font-bold text-brand-800">Survei telah Anda isi</p>
                <p class="text-sm text-brand-700">Dikirim pada {{ $response->submitted_at->translatedFormat('l, d F Y \p\u\k\u\l H:i') }}. Terima kasih atas partisipasi Anda!</p>
            </div>
            @if ($response->average_score)
                <div class="rounded-2xl bg-white px-5 py-3 text-center">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Skor Anda</p>
                    <p class="text-2xl font-extrabold text-slate-900">{{ number_format($response->average_score, 1, ',', '.') }}<span class="text-sm text-slate-400">/5</span></p>
                </div>
            @endif
        </div>

        <div class="card divide-y divide-slate-100">
            @foreach ($survey->questions as $i => $question)
                @php $answer = $answers->get($question->id); @endphp
                <div class="p-4 sm:p-6">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pertanyaan {{ $i + 1 }}</p>
                    <p class="mt-1 font-semibold text-slate-800">{{ $question->question_text }}</p>
                    <div class="mt-3">
                        @if (! $answer)
                            <span class="text-sm italic text-slate-400">Tidak dijawab</span>
                        @elseif ($question->question_type === 'rating')
                            <div class="flex flex-wrap items-center gap-3">
                                <x-stars :value="$answer->answer_value" size="text-lg" />
                                <span class="text-sm font-semibold text-slate-600">{{ \App\Models\SurveyQuestion::RATING_LABELS[$answer->answer_value] ?? '' }}</span>
                            </div>
                        @else
                            <p class="rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-700">{{ $answer->answer_text }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
            @if ($response->suggestion)
                <div class="p-4 sm:p-6">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Saran & Masukan</p>
                    <p class="mt-2 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-900">{{ $response->suggestion }}</p>
                </div>
            @endif
        </div>
    @else
        {{-- ============ Form pengisian ============ --}}
        @php
            $requiredIds = $survey->questions->where('is_required', true)->pluck('id')->values();
        @endphp

        <form method="POST" action="{{ route('app.surveys.store', $survey) }}"
              x-data="surveyForm(@js($requiredIds), {{ $survey->questions->count() }})" @submit="submit($event)">
            @csrf

            {{-- Progres --}}
            <div class="sticky top-16 z-30 -mx-4 mb-5 border-b border-slate-200 bg-slate-50/95 px-4 py-3 backdrop-blur sm:-mx-6 sm:px-6 lg:top-0 lg:-mx-10 lg:px-10">
                <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                    <span><span x-text="answered"></span> dari {{ $survey->questions->count() }} pertanyaan dijawab</span>
                    <span class="text-brand-700" x-text="progress + '%'"></span>
                </div>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200">
                    <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-sky-500 transition-all duration-300" :style="`width: ${progress}%`"></div>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <p class="font-bold"><i class="fa-solid fa-circle-exclamation mr-1"></i>Mohon lengkapi jawaban berikut:</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-4">
                @foreach ($survey->questions as $i => $question)
                    @php $old = old('answers.'.$question->id); @endphp
                    <div id="question-{{ $question->id }}" class="card scroll-mt-40 p-4 transition sm:p-6 @error('answers.'.$question->id) ring-2 ring-rose-300 @enderror">
                        <div class="flex gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-sm font-extrabold text-brand-700">{{ $i + 1 }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-slate-800">
                                    {{ $question->question_text }}
                                    @if ($question->is_required)<span class="text-rose-500" title="Wajib">*</span>@else<span class="ml-1 text-xs font-normal text-slate-400">(opsional)</span>@endif
                                </p>

                                @if ($question->question_type === 'rating')
                                    <div class="mt-3" x-data="{ val: {{ (int) $old }}, hover: 0 }" x-init="val && set({{ $question->id }}, val)">
                                        <div class="star-input flex items-center gap-1.5 sm:gap-2" @mouseleave="hover = 0">
                                            @for ($s = 1; $s <= 5; $s++)
                                                <label :class="(hover || val) >= {{ $s }} && 'on'" @mouseenter="hover = {{ $s }}" title="{{ \App\Models\SurveyQuestion::RATING_LABELS[$s] }}">
                                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $s }}" class="sr-only"
                                                           @checked((int) $old === $s) @change="val = {{ $s }}; set({{ $question->id }}, {{ $s }})">
                                                    <i class="fa-solid fa-star"></i>
                                                </label>
                                            @endfor
                                        </div>
                                        <p class="mt-1.5 h-5 text-sm font-semibold"
                                           :class="(hover || val) >= 4 ? 'text-brand-600' : ((hover || val) >= 3 ? 'text-amber-600' : 'text-rose-500')"
                                           x-text="@js(\App\Models\SurveyQuestion::RATING_LABELS)[hover || val] ?? ''"></p>
                                    </div>
                                @elseif ($question->question_type === 'multiple_choice')
                                    <div class="mt-3 grid gap-2 sm:grid-cols-2" x-init="@js($old) && set({{ $question->id }}, @js($old))">
                                        @foreach ($question->options ?? [] as $option)
                                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 transition hover:border-brand-300 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:font-semibold has-[:checked]:text-brand-800">
                                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" class="h-4 w-4 accent-brand-600"
                                                       @checked($old === $option) @change="set({{ $question->id }}, $event.target.value)">
                                                {{ $option }}
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <textarea name="answers[{{ $question->id }}]" rows="3" maxlength="2000" placeholder="Tulis jawaban Anda..."
                                              class="input mt-3" x-init="$el.value && set({{ $question->id }}, $el.value)"
                                              @input="set({{ $question->id }}, $event.target.value.trim())">{{ $old }}</textarea>
                                @endif
                                <x-field-error :name="'answers.'.$question->id" />
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="card p-4 sm:p-6">
                    <label for="suggestion" class="label">Saran / Masukan Tambahan <span class="font-normal normal-case text-slate-400">(opsional)</span></label>
                    <textarea id="suggestion" name="suggestion" rows="3" maxlength="2000" placeholder="Tulis masukan Anda untuk peningkatan layanan kami..." class="input">{{ old('suggestion') }}</textarea>
                    <x-field-error name="suggestion" />
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-slate-500"><i class="fa-solid fa-lock mr-1"></i>Jawaban tidak dapat diubah setelah dikirim.</p>
                <button type="submit" class="btn btn-primary btn-lg w-full sm:w-auto" data-loading="Mengirim jawaban..."
                        :class="missing.length && 'opacity-80'">
                    <i class="fa-solid fa-paper-plane"></i>Kirim Jawaban Survei
                </button>
            </div>
            <p x-cloak x-show="tried && missing.length" class="mt-3 text-right text-xs font-semibold text-rose-500">
                Masih ada <span x-text="missing.length"></span> pertanyaan wajib yang belum dijawab.
            </p>
        </form>
    @endif
</x-layout.customer>
