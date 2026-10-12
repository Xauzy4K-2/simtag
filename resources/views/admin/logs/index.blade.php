<x-layout.admin title="Log Aktivitas">
    <x-page-header title="Log Aktivitas" subtitle="Jejak audit seluruh aktivitas administrator dan petugas." />

    <form method="GET" class="card mb-5 grid gap-3 p-4 sm:grid-cols-[1fr_auto_auto]">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
            <input name="search" value="{{ request('search') }}" placeholder="Cari aktivitas..." class="input pl-10">
        </div>
        <select name="admin_id" class="input sm:w-56">
            <option value="">Semua pengguna</option>
            @foreach ($admins as $admin)
                <option value="{{ $admin->id }}" @selected(request('admin_id') == $admin->id)>{{ $admin->name }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary"><i class="fa-solid fa-filter"></i>Filter</button>
    </form>

    <div class="card overflow-hidden">
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aktivitas</th><th class="hidden lg:table-cell">IP</th></tr></thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="whitespace-nowrap text-xs"><p class="font-semibold text-slate-700">{{ $log->created_at->translatedFormat('d M Y') }}</p><p class="text-slate-400">{{ $log->created_at->format('H:i:s') }}</p></td>
                            <td class="whitespace-nowrap"><p class="font-semibold text-slate-800">{{ $log->admin?->name ?? 'Sistem' }}</p><p class="text-xs text-slate-400">{{ $log->admin?->role_label }}</p></td>
                            <td class="min-w-64"><p class="text-slate-700">{{ $log->activity }}</p>@if ($log->details)<p class="mt-0.5 text-xs text-slate-400">{{ \Illuminate\Support\Str::limit($log->details, 120) }}</p>@endif</td>
                            <td class="hidden font-mono text-xs text-slate-400 lg:table-cell">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><x-empty-state icon="fa-list-check" title="Belum ada aktivitas" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $logs->links() }}</div>
</x-layout.admin>
