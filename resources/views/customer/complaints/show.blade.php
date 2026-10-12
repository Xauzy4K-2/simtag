<x-layout.customer :title="'Pengaduan '.$complaint->ticket_number">
    <x-page-header :title="$complaint->subject" :eyebrow="$complaint->ticket_number" :back="route('app.complaints.index')">
        <x-slot:actions><x-status-badge :status="$complaint->status" class="text-xs" /></x-slot:actions>
    </x-page-header>

    @php
        $steps = ['open' => 'Diterima', 'in_progress' => 'Diproses', 'resolved' => 'Selesai'];
        $order = array_keys($steps);
        $current = $complaint->status === 'closed' ? 2 : array_search($complaint->status, $order);
    @endphp

    {{-- Linimasa status --}}
    <div class="card mb-6 p-4 sm:p-6">
        <ol class="flex items-center">
            @foreach ($steps as $key => $label)
                @php $done = $loop->index <= $current; @endphp
                <li class="flex flex-1 items-center {{ $loop->last ? 'flex-none' : '' }}">
                    <div class="flex flex-col items-center gap-1.5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full text-sm {{ $done ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-400' }}">
                            <i class="fa-solid {{ ['fa-inbox', 'fa-screwdriver-wrench', 'fa-circle-check'][$loop->index] }}"></i>
                        </span>
                        <span class="text-[11px] font-semibold {{ $done ? 'text-brand-700' : 'text-slate-400' }}">{{ $label }}</span>
                    </div>
                    @unless ($loop->last)
                        <div class="mx-2 mb-5 h-1 flex-1 rounded-full {{ $loop->index < $current ? 'bg-brand-500' : 'bg-slate-100' }}"></div>
                    @endunless
                </li>
            @endforeach
        </ol>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="card card-body">
                <h2 class="card-title mb-3">Detail Pengaduan</h2>
                <p class="whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $complaint->description }}</p>
                @if ($complaint->attachment_url)
                    <a href="{{ $complaint->attachment_url }}" target="_blank" class="mt-4 block">
                        <img src="{{ $complaint->attachment_url }}" alt="Lampiran pengaduan" class="max-h-80 w-full rounded-2xl border border-slate-200 object-cover">
                    </a>
                @endif
            </section>

            <section class="card card-body">
                <h2 class="card-title mb-3"><i class="fa-solid fa-reply mr-1.5 text-brand-500"></i>Tanggapan PDAM</h2>
                @if ($complaint->admin_response)
                    <div class="rounded-2xl bg-brand-50 p-4">
                        <p class="whitespace-pre-line text-sm text-brand-900">{{ $complaint->admin_response }}</p>
                        <p class="mt-3 text-xs text-brand-700">
                            {{ $complaint->handler?->name ?? 'Petugas PDAM' }} · {{ $complaint->handled_at?->translatedFormat('d M Y, H:i') }}
                        </p>
                    </div>
                @else
                    <p class="text-sm text-slate-500">Belum ada tanggapan. Pengaduan Anda sedang dalam antrean dan akan segera ditinjau petugas.</p>
                @endif
            </section>
        </div>

        <aside class="card card-body h-fit">
            <dl class="space-y-4 text-sm">
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Nomor Tiket</dt><dd class="mt-1 font-mono font-bold text-slate-800">{{ $complaint->ticket_number }}</dd></div>
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Kategori</dt><dd class="mt-1 font-semibold text-slate-800">{{ $complaint->category }}</dd></div>
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Lokasi</dt><dd class="mt-1 text-slate-700">{{ $complaint->location ?: '-' }}</dd></div>
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Dikirim</dt><dd class="mt-1 text-slate-700">{{ $complaint->created_at->translatedFormat('d F Y, H:i') }}</dd></div>
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Pembaruan Terakhir</dt><dd class="mt-1 text-slate-700">{{ $complaint->updated_at->diffForHumans() }}</dd></div>
            </dl>
        </aside>
    </div>
</x-layout.customer>
