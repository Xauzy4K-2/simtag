<x-layout.admin title="Pengaduan">
    <x-page-header title="Pengaduan Pelanggan" subtitle="Tinjau, tanggapi, dan selesaikan laporan gangguan layanan." />

    {{-- Ringkasan status --}}
    <div class="mb-5 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach (\App\Models\Complaint::STATUSES as $key => $label)
            @php $color = ['open' => 'text-amber-600 bg-amber-50', 'in_progress' => 'text-sky-600 bg-sky-50', 'resolved' => 'text-emerald-600 bg-emerald-50', 'closed' => 'text-slate-600 bg-slate-100'][$key]; @endphp
            <a href="{{ route('admin.complaints.index', ['status' => $key]) }}" class="card flex items-center gap-3 p-4 transition hover:shadow-md {{ request('status') === $key ? 'ring-2 ring-brand-500' : '' }}">
                <span class="stat-icon {{ $color }}"><i class="fa-solid {{ ['open' => 'fa-inbox', 'in_progress' => 'fa-screwdriver-wrench', 'resolved' => 'fa-circle-check', 'closed' => 'fa-lock'][$key] }}"></i></span>
                <div><p class="text-2xl font-extrabold text-slate-900">{{ $counts[$key] ?? 0 }}</p><p class="text-xs font-semibold text-slate-500">{{ $label }}</p></div>
            </a>
        @endforeach
    </div>

    <form method="GET" class="card mb-5 grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_auto_auto_auto]">
        <div class="relative sm:col-span-2 lg:col-span-1">
            <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
            <input name="search" value="{{ request('search') }}" placeholder="Cari tiket, judul, atau nama pelanggan..." class="input pl-10">
        </div>
        <select name="category" class="input lg:w-56">
            <option value="">Semua kategori</option>
            @foreach (\App\Models\Complaint::CATEGORIES as $category)
                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
            @endforeach
        </select>
        <select name="status" class="input lg:w-40">
            <option value="">Semua status</option>
            @foreach (\App\Models\Complaint::STATUSES as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <div class="flex gap-2 sm:col-span-2 lg:col-span-1">
            <button class="btn btn-primary flex-1"><i class="fa-solid fa-filter"></i>Filter</button>
            @if (request()->hasAny(['search', 'status', 'category']))
                <a href="{{ route('admin.complaints.index') }}" class="btn btn-secondary" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
            @endif
        </div>
    </form>

    <div class="card overflow-hidden">
        <div class="table-wrap hidden md:block">
            <table class="table">
                <thead><tr><th>Tiket</th><th>Pelanggan</th><th>Pengaduan</th><th>Tanggal</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($complaints as $complaint)
                        <tr>
                            <td class="whitespace-nowrap font-mono text-xs font-bold text-slate-600">{{ $complaint->ticket_number }}</td>
                            <td><p class="font-bold text-slate-800">{{ $complaint->customer?->full_name }}</p><p class="text-xs text-slate-400">{{ $complaint->customer?->district }}</p></td>
                            <td class="max-w-xs"><p class="truncate font-semibold text-slate-700">{{ $complaint->subject }}</p><p class="text-xs text-slate-400">{{ $complaint->category }}</p></td>
                            <td class="whitespace-nowrap text-xs"><p class="font-semibold text-slate-700">{{ $complaint->created_at->translatedFormat('d M Y') }}</p><p class="text-slate-400">{{ $complaint->created_at->format('H:i') }} · {{ $complaint->created_at->diffForHumans() }}</p></td>
                            <td><x-status-badge :status="$complaint->status" /></td>
                            <td class="text-right"><a href="{{ route('admin.complaints.show', $complaint) }}" class="btn btn-secondary btn-sm">Tangani</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty-state icon="fa-inbox" title="Tidak ada pengaduan" description="Tidak ada pengaduan yang sesuai filter." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-slate-100 md:hidden">
            @forelse ($complaints as $complaint)
                <a href="{{ route('admin.complaints.show', $complaint) }}" class="block p-4">
                    <div class="flex items-start justify-between gap-2">
                        <p class="font-mono text-xs font-bold text-slate-400">{{ $complaint->ticket_number }}</p>
                        <x-status-badge :status="$complaint->status" />
                    </div>
                    <p class="mt-1 font-bold text-slate-800">{{ $complaint->subject }}</p>
                    <p class="text-xs text-slate-500">{{ $complaint->customer?->full_name }} · {{ $complaint->category }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ $complaint->created_at->translatedFormat('d M Y, H:i') }}</p>
                </a>
            @empty
                <x-empty-state icon="fa-inbox" title="Tidak ada pengaduan" />
            @endforelse
        </div>
    </div>

    <div class="mt-5">{{ $complaints->links() }}</div>
</x-layout.admin>
