<x-layout.admin title="Pelanggan">
    <x-page-header title="Data Pelanggan" :subtitle="$customers->total().' pelanggan terdaftar'">
        <x-slot:actions>
            <a href="{{ route('admin.customers.create') }}" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i>Tambah Pelanggan</a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="card mb-5 grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_auto_auto_auto]">
        <div class="relative sm:col-span-2 lg:col-span-1">
            <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
            <input name="search" value="{{ request('search') }}" placeholder="Cari nama, nomor pelanggan, email, atau HP..." class="input pl-10">
        </div>
        <select name="district" class="input lg:w-48">
            <option value="">Semua kecamatan</option>
            @foreach (\App\Models\Customer::DISTRICTS as $district)
                <option value="{{ $district }}" @selected(request('district') === $district)>{{ $district }}</option>
            @endforeach
        </select>
        <select name="status" class="input lg:w-40">
            <option value="">Semua status</option>
            @foreach (\App\Models\Customer::STATUSES as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <div class="flex gap-2 sm:col-span-2 lg:col-span-1">
            <button class="btn btn-primary flex-1"><i class="fa-solid fa-filter"></i>Filter</button>
            @if (request()->hasAny(['search', 'status', 'district']))
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
            @endif
        </div>
    </form>

    <div class="card overflow-hidden">
        {{-- Tabel (tablet & desktop) --}}
        <div class="table-wrap hidden md:block">
            <table class="table">
                <thead>
                    <tr><th>Pelanggan</th><th>No. Pelanggan</th><th>Kontak</th><th>Kecamatan</th><th class="text-center">Survei</th><th class="text-center">Aduan</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td>
                                <a href="{{ route('admin.customers.show', $customer) }}" class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700">{{ $customer->initials }}</span>
                                    <span class="font-bold text-slate-800 hover:text-brand-700">{{ $customer->full_name }}</span>
                                </a>
                            </td>
                            <td class="font-mono text-xs">{{ $customer->customer_number }}</td>
                            <td><p class="text-slate-700">{{ $customer->email }}</p><p class="text-xs text-slate-400">{{ $customer->phone }}</p></td>
                            <td>{{ $customer->district ?? '-' }}</td>
                            <td class="text-center font-semibold">{{ $customer->survey_responses_count }}</td>
                            <td class="text-center font-semibold">{{ $customer->complaints_count }}</td>
                            <td><x-status-badge :status="$customer->status" /></td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-ghost btn-sm" title="Detail"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-ghost btn-sm" title="Ubah"><i class="fa-solid fa-pen"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><x-empty-state icon="fa-users" title="Pelanggan tidak ditemukan" description="Coba ubah kata kunci atau filter pencarian." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Kartu (ponsel) --}}
        <div class="divide-y divide-slate-100 md:hidden">
            @forelse ($customers as $customer)
                <a href="{{ route('admin.customers.show', $customer) }}" class="flex items-start gap-3 p-4">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700">{{ $customer->initials }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="truncate font-bold text-slate-800">{{ $customer->full_name }}</p>
                            <x-status-badge :status="$customer->status" />
                        </div>
                        <p class="font-mono text-xs text-slate-500">{{ $customer->customer_number }}</p>
                        <p class="mt-1 truncate text-xs text-slate-500">{{ $customer->phone }} · {{ $customer->district ?? '-' }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ $customer->survey_responses_count }} survei · {{ $customer->complaints_count }} pengaduan</p>
                    </div>
                </a>
            @empty
                <x-empty-state icon="fa-users" title="Pelanggan tidak ditemukan" />
            @endforelse
        </div>
    </div>

    <div class="mt-5">{{ $customers->links() }}</div>
</x-layout.admin>
