@php $editing = $survey->exists; @endphp

<x-layout.admin :title="$editing ? 'Ubah Survei' : 'Buat Survei'">
    <x-page-header :title="$editing ? 'Ubah Informasi Survei' : 'Buat Survei Baru'"
                   :subtitle="$editing ? null : 'Survei dibuat sebagai draf. Setelah menambahkan pertanyaan, Anda dapat mempublikasikannya.'"
                   :back="$editing ? route('admin.surveys.show', $survey) : route('admin.surveys.index')" />

    <form method="POST" action="{{ $editing ? route('admin.surveys.update', $survey) : route('admin.surveys.store') }}" class="card max-w-3xl">
        @csrf
        @if ($editing) @method('PUT') @endif
        <div class="card-body space-y-5">
            <div>
                <label for="title" class="label">Judul Survei</label>
                <input id="title" name="title" value="{{ old('title', $survey->title) }}" required maxlength="200" placeholder="Contoh: Survei Kepuasan Pelanggan Semester II" class="input @error('title') input-error @enderror">
                <x-field-error name="title" />
            </div>
            <div>
                <label for="description" class="label">Deskripsi / Pengantar</label>
                <textarea id="description" name="description" rows="4" maxlength="2000" placeholder="Jelaskan tujuan survei kepada pelanggan..." class="input @error('description') input-error @enderror">{{ old('description', $survey->description) }}</textarea>
                <x-field-error name="description" />
            </div>
            <div class="sm:w-1/2">
                <label for="ends_at" class="label">Batas Akhir Pengisian <span class="font-normal normal-case text-slate-400">(opsional)</span></label>
                <input id="ends_at" name="ends_at" type="date" value="{{ old('ends_at', $survey->ends_at?->toDateString()) }}" class="input @error('ends_at') input-error @enderror">
                <p class="hint">Kosongkan jika survei tidak memiliki batas waktu.</p>
                <x-field-error name="ends_at" />
            </div>
        </div>
        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 px-4 py-4 sm:flex-row sm:justify-end sm:px-6">
            <a href="{{ $editing ? route('admin.surveys.show', $survey) : route('admin.surveys.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" data-loading="Menyimpan..."><i class="fa-solid fa-floppy-disk"></i>{{ $editing ? 'Simpan Perubahan' : 'Buat & Tambah Pertanyaan' }}</button>
        </div>
    </form>
</x-layout.admin>
