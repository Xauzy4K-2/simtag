{{-- Field form pertanyaan; dipakai untuk tambah & ubah. Variabel: $question (nullable), $prefix (id unik) --}}
@php
    // Input lama (setelah validasi gagal) hanya diterapkan ke form tambah agar tidak "bocor" ke form ubah lain.
    $useOld = $prefix === 'new';
    $type = $useOld ? old('question_type', 'rating') : $question->question_type;
    $optionsText = $useOld ? old('options_text', '') : implode("\n", $question->options ?? []);
    $text = $useOld ? old('question_text') : $question->question_text;
    $required = $useOld ? (bool) old('is_required', true) : $question->is_required;
@endphp

<div class="grid gap-4" x-data="{ type: @js($type) }">
    <div>
        <label for="{{ $prefix }}-text" class="label">Teks Pertanyaan</label>
        <textarea id="{{ $prefix }}-text" name="question_text" rows="2" required maxlength="1000" placeholder="Contoh: Bagaimana kualitas kejernihan air di rumah Anda?" class="input">{{ $text }}</textarea>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="{{ $prefix }}-type" class="label">Tipe Jawaban</label>
            <select id="{{ $prefix }}-type" name="question_type" x-model="type" class="input">
                @foreach (\App\Models\SurveyQuestion::TYPES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <label class="flex items-center gap-3 self-end rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700">
            <input type="hidden" name="is_required" value="0">
            <input type="checkbox" name="is_required" value="1" class="h-4 w-4 accent-brand-600" @checked($required)>
            Wajib dijawab
        </label>
    </div>
    <div x-show="type === 'multiple_choice'" x-cloak>
        <label for="{{ $prefix }}-options" class="label">Pilihan Jawaban</label>
        <textarea id="{{ $prefix }}-options" name="options_text" rows="4" placeholder="Satu pilihan per baris&#10;Contoh:&#10;Sangat setuju&#10;Setuju&#10;Tidak setuju" class="input font-mono text-xs" :disabled="type !== 'multiple_choice'">{{ $optionsText }}</textarea>
        <p class="hint">Tulis satu pilihan per baris, minimal 2 pilihan.</p>
    </div>
    <p x-show="type === 'rating'" class="rounded-xl bg-amber-50 px-3 py-2 text-xs text-amber-800"><i class="fa-solid fa-star mr-1"></i>Pelanggan memberi nilai 1 (Sangat Tidak Puas) sampai 5 (Sangat Puas). Nilai ini dihitung dalam indeks kepuasan.</p>
    <p x-show="type === 'text'" x-cloak class="rounded-xl bg-sky-50 px-3 py-2 text-xs text-sky-800"><i class="fa-solid fa-pen mr-1"></i>Pelanggan menulis jawaban bebas (maks. 2000 karakter).</p>
</div>
