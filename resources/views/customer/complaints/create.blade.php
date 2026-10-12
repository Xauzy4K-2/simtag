<x-layout.customer title="Buat Pengaduan">
    <x-page-header title="Form Pengaduan" subtitle="Jelaskan kendala Anda sedetail mungkin agar petugas dapat menindaklanjuti dengan cepat." :back="route('app.complaints.index')" />

    <div class="grid gap-6 lg:grid-cols-3">
        <form method="POST" action="{{ route('app.complaints.store') }}" enctype="multipart/form-data" class="card card-body space-y-5 lg:col-span-2"
              x-data="{ preview: null, name: '' }">
            @csrf

            <div>
                <label for="category" class="label">Kategori Masalah</label>
                <select id="category" name="category" required class="input @error('category') input-error @enderror">
                    <option value="">Pilih kategori</option>
                    @foreach (\App\Models\Complaint::CATEGORIES as $category)
                        <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
                <x-field-error name="category" />
            </div>

            <div>
                <label for="subject" class="label">Judul Pengaduan</label>
                <input id="subject" name="subject" value="{{ old('subject') }}" required maxlength="200" placeholder="Contoh: Air tidak mengalir sejak pagi" class="input @error('subject') input-error @enderror">
                <x-field-error name="subject" />
            </div>

            <div>
                <label for="description" class="label">Deskripsi Masalah</label>
                <textarea id="description" name="description" rows="5" required maxlength="5000" placeholder="Ceritakan kronologi, sejak kapan, dan dampaknya..." class="input @error('description') input-error @enderror">{{ old('description') }}</textarea>
                <x-field-error name="description" />
            </div>

            <div>
                <label for="location" class="label">Lokasi Kejadian <span class="font-normal normal-case text-slate-400">(opsional)</span></label>
                <input id="location" name="location" value="{{ old('location', $customer->address) }}" maxlength="255" placeholder="Alamat atau patokan lokasi" class="input @error('location') input-error @enderror">
                <x-field-error name="location" />
            </div>

            <div>
                <span class="label">Foto Pendukung <span class="font-normal normal-case text-slate-400">(opsional, maks. 5 MB)</span></span>
                <label class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-6 text-center transition hover:border-brand-300 hover:bg-brand-50/40">
                    <template x-if="preview"><img :src="preview" alt="Pratinjau" class="max-h-48 rounded-xl object-contain"></template>
                    <template x-if="!preview"><i class="fa-solid fa-camera text-3xl text-slate-300"></i></template>
                    <span class="text-sm font-semibold text-slate-600" x-text="name || 'Ketuk untuk memilih foto'"></span>
                    <span class="text-xs text-slate-400">JPG, PNG, atau WEBP</span>
                    <input type="file" name="attachment" accept="image/jpeg,image/png,image/webp" class="sr-only"
                           @change="const f = $event.target.files[0]; name = f ? f.name : ''; preview = f ? URL.createObjectURL(f) : null">
                </label>
                <x-field-error name="attachment" />
            </div>

            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <a href="{{ route('app.complaints.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-danger" data-loading="Mengirim..."><i class="fa-solid fa-paper-plane"></i>Kirim Pengaduan</button>
            </div>
        </form>

        <aside class="space-y-4">
            <div class="rounded-2xl border border-sky-100 bg-sky-50 p-5 text-sm">
                <p class="font-bold text-sky-800"><i class="fa-solid fa-id-card mr-1.5"></i>Data Pelapor</p>
                <dl class="mt-3 space-y-2 text-sky-900">
                    <div><dt class="text-xs text-sky-600">Nama</dt><dd class="font-semibold">{{ $customer->full_name }}</dd></div>
                    <div><dt class="text-xs text-sky-600">No. Pelanggan</dt><dd class="font-mono font-semibold">{{ $customer->customer_number }}</dd></div>
                    <div><dt class="text-xs text-sky-600">Telepon</dt><dd class="font-semibold">{{ $customer->phone }}</dd></div>
                </dl>
            </div>
            <div class="card p-5 text-sm text-slate-600">
                <p class="font-bold text-slate-800"><i class="fa-solid fa-lightbulb mr-1.5 text-amber-500"></i>Tips</p>
                <ul class="mt-3 list-inside list-disc space-y-1.5">
                    <li>Sertakan foto agar petugas mudah mengidentifikasi masalah.</li>
                    <li>Cantumkan patokan lokasi bila kejadian di jalan umum.</li>
                    <li>Simpan nomor tiket untuk memantau status.</li>
                </ul>
            </div>
        </aside>
    </div>
</x-layout.customer>
