<x-layout.admin :title="'Pengaduan '.$complaint->ticket_number">
    <x-page-header :title="$complaint->subject" :eyebrow="$complaint->ticket_number" :back="route('admin.complaints.index')">
        <x-slot:actions>
            <x-status-badge :status="$complaint->status" class="text-xs" />
            @if (auth()->user()->isAdmin())
                <form method="POST" action="{{ route('admin.complaints.destroy', $complaint) }}" data-confirm="Hapus pengaduan ini secara permanen?">
                    @csrf @method('DELETE')
                    <button class="btn btn-soft-danger btn-sm"><i class="fa-solid fa-trash"></i>Hapus</button>
                </form>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="card card-body">
                <div class="mb-4 flex flex-wrap gap-2">
                    <span class="badge badge-slate"><i class="fa-solid fa-tag"></i>{{ $complaint->category }}</span>
                    <span class="badge badge-slate"><i class="fa-regular fa-clock"></i>{{ $complaint->created_at->translatedFormat('d M Y, H:i') }}</span>
                </div>
                <p class="whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $complaint->description }}</p>
                @if ($complaint->location)
                    <p class="mt-4 flex gap-2 rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-600"><i class="fa-solid fa-location-dot mt-0.5 text-rose-500"></i>{{ $complaint->location }}</p>
                @endif
                @if ($complaint->attachment_url)
                    <a href="{{ $complaint->attachment_url }}" target="_blank" class="mt-4 block">
                        <img src="{{ $complaint->attachment_url }}" alt="Lampiran" class="max-h-96 w-full rounded-2xl border border-slate-200 object-contain">
                    </a>
                @endif
            </section>

            <form method="POST" action="{{ route('admin.complaints.update', $complaint) }}" class="card">
                @csrf @method('PUT')
                <div class="card-header"><h2 class="card-title"><i class="fa-solid fa-reply mr-1.5 text-brand-500"></i>Tanggapan & Status</h2></div>
                <div class="card-body space-y-5">
                    <div>
                        <span class="label">Status Pengaduan</span>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                            @foreach (\App\Models\Complaint::STATUSES as $key => $label)
                                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-600 transition has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                                    <input type="radio" name="status" value="{{ $key }}" class="sr-only" @checked(old('status', $complaint->status) === $key)>{{ $label }}
                                </label>
                            @endforeach
                        </div>
                        <x-field-error name="status" />
                    </div>
                    <div>
                        <label for="admin_response" class="label">Tanggapan untuk Pelanggan</label>
                        <textarea id="admin_response" name="admin_response" rows="5" maxlength="5000" placeholder="Tuliskan tindak lanjut yang sudah/akan dilakukan..." class="input @error('admin_response') input-error @enderror">{{ old('admin_response', $complaint->admin_response) }}</textarea>
                        <p class="hint">Tanggapan akan terlihat oleh pelanggan di halaman pengaduannya.</p>
                        <x-field-error name="admin_response" />
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach (['Laporan sudah kami terima dan tim teknis sedang menuju lokasi.', 'Perbaikan sudah selesai dilakukan. Terima kasih atas laporan Anda.', 'Mohon maaf atas ketidaknyamanannya, gangguan disebabkan pemeliharaan jaringan.'] as $template)
                            <button type="button" class="rounded-lg bg-slate-100 px-3 py-1.5 text-left text-xs text-slate-600 hover:bg-brand-50 hover:text-brand-700"
                                    onclick="document.getElementById('admin_response').value = this.dataset.text" data-text="{{ $template }}">
                                <i class="fa-solid fa-bolt mr-1 text-amber-500"></i>{{ \Illuminate\Support\Str::limit($template, 48) }}
                            </button>
                        @endforeach
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary w-full sm:w-auto" data-loading="Menyimpan..."><i class="fa-solid fa-paper-plane"></i>Simpan Tanggapan</button>
                    </div>
                </div>
            </form>
        </div>

        <aside class="space-y-6">
            <section class="card card-body">
                <h2 class="card-title mb-4">Pelapor</h2>
                @if ($complaint->customer)
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">{{ $complaint->customer->initials }}</span>
                        <div class="min-w-0">
                            <p class="truncate font-bold text-slate-800">{{ $complaint->customer->full_name }}</p>
                            <p class="font-mono text-xs text-slate-500">{{ $complaint->customer->customer_number }}</p>
                        </div>
                    </div>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex gap-2"><i class="fa-solid fa-phone mt-1 w-4 text-slate-400"></i><a href="tel:{{ $complaint->customer->phone }}" class="text-brand-700 hover:underline">{{ $complaint->customer->phone }}</a></div>
                        <div class="flex gap-2"><i class="fa-brands fa-whatsapp mt-1 w-4 text-slate-400"></i><a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $complaint->customer->phone)) }}" target="_blank" rel="noopener" class="text-brand-700 hover:underline">Hubungi via WhatsApp</a></div>
                        <div class="flex gap-2"><i class="fa-solid fa-envelope mt-1 w-4 text-slate-400"></i><span class="break-all text-slate-600">{{ $complaint->customer->email }}</span></div>
                        <div class="flex gap-2"><i class="fa-solid fa-map mt-1 w-4 text-slate-400"></i><span class="text-slate-600">{{ $complaint->customer->district ?? '-' }}</span></div>
                    </dl>
                    <a href="{{ route('admin.customers.show', $complaint->customer) }}" class="btn btn-secondary btn-sm mt-4 w-full">Lihat profil pelanggan</a>
                @endif
            </section>

            <section class="card card-body">
                <h2 class="card-title mb-4">Penanganan</h2>
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-xs text-slate-400">Ditangani oleh</dt><dd class="font-semibold text-slate-700">{{ $complaint->handler?->name ?? 'Belum ditangani' }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Terakhir diperbarui</dt><dd class="font-semibold text-slate-700">{{ $complaint->handled_at?->translatedFormat('d M Y, H:i') ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Lama sejak dilaporkan</dt><dd class="font-semibold text-slate-700">{{ $complaint->created_at->diffForHumans(null, true) }}</dd></div>
                </dl>
            </section>
        </aside>
    </div>
</x-layout.admin>
