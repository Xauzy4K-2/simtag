<x-layout.customer title="Pengaduan">
    <x-page-header title="Pengaduan Saya" subtitle="Laporkan kendala layanan air dan pantau proses penanganannya.">
        <x-slot:actions>
            <a href="{{ route('app.complaints.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i>Buat Pengaduan</a>
        </x-slot:actions>
    </x-page-header>

    <div class="no-scrollbar -mx-4 mb-5 flex gap-2 overflow-x-auto px-4 sm:mx-0 sm:px-0">
        @foreach (['' => 'Semua'] + \App\Models\Complaint::STATUSES as $key => $label)
            <a href="{{ route('app.complaints.index', array_filter(['status' => $key])) }}"
               class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition {{ (string) request('status') === (string) $key ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-brand-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="space-y-3">
        @forelse ($complaints as $complaint)
            <a href="{{ route('app.complaints.show', $complaint) }}" class="card block p-4 transition hover:-translate-y-0.5 hover:shadow-md sm:p-5">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="font-mono text-xs font-bold text-slate-400">{{ $complaint->ticket_number }}</p>
                        <h3 class="mt-0.5 font-bold text-slate-900">{{ $complaint->subject }}</h3>
                    </div>
                    <x-status-badge :status="$complaint->status" />
                </div>
                <p class="mt-2 line-clamp-2 text-sm text-slate-500">{{ $complaint->description }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                    <span><i class="fa-solid fa-tag mr-1 text-slate-400"></i>{{ $complaint->category }}</span>
                    <span><i class="fa-regular fa-clock mr-1 text-slate-400"></i>{{ $complaint->created_at->translatedFormat('d M Y, H:i') }}</span>
                    @if ($complaint->admin_response)<span class="font-semibold text-brand-600"><i class="fa-solid fa-reply mr-1"></i>Ada tanggapan</span>@endif
                </div>
            </a>
        @empty
            <div class="card">
                <x-empty-state icon="fa-headset" title="Belum ada pengaduan" description="Jika mengalami gangguan layanan air, kirimkan pengaduan agar segera kami tindak lanjuti.">
                    <a href="{{ route('app.complaints.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i>Buat Pengaduan</a>
                </x-empty-state>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $complaints->links() }}</div>
</x-layout.customer>
