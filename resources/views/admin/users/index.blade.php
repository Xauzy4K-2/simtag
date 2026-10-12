<x-layout.admin title="Pengguna Sistem">
    <x-page-header title="Pengguna Sistem" subtitle="Kelola akun administrator dan petugas layanan.">
        <x-slot:actions>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i>Tambah Pengguna</a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="mb-5 sm:w-80">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
            <input name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="input bg-white pl-10">
        </div>
    </form>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($users as $user)
            <article class="card card-body">
                <div class="flex items-start gap-3">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full {{ $user->isAdmin() ? 'bg-slate-900 text-brand-300' : 'bg-sky-100 text-sky-700' }} font-bold">{{ $user->initials }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-bold text-slate-900">{{ $user->name }} @if ($user->is(auth()->user()))<span class="text-xs font-semibold text-brand-600">(Anda)</span>@endif</p>
                        <p class="truncate text-sm text-slate-500">{{ $user->email }}</p>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            <span class="badge {{ $user->isAdmin() ? 'badge-violet' : 'badge-blue' }}"><i class="fa-solid {{ $user->isAdmin() ? 'fa-user-shield' : 'fa-user-tie' }}"></i>{{ $user->role_label }}</span>
                            <x-status-badge :status="$user->status" />
                        </div>
                    </div>
                </div>
                <p class="mt-4 text-xs text-slate-400"><i class="fa-regular fa-clock mr-1"></i>Login terakhir: {{ $user->last_login_at?->diffForHumans() ?? 'belum pernah' }}</p>
                <div class="mt-4 flex gap-2 border-t border-slate-100 pt-4">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary btn-sm flex-1"><i class="fa-solid fa-pen"></i>Ubah</a>
                    @unless ($user->is(auth()->user()))
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm="Hapus pengguna {{ $user->name }}?">
                            @csrf @method('DELETE')
                            <button class="btn btn-soft-danger btn-sm" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    @endunless
                </div>
            </article>
        @empty
            <div class="card sm:col-span-2 xl:col-span-3"><x-empty-state icon="fa-user-shield" title="Pengguna tidak ditemukan" /></div>
        @endforelse
    </div>

    <div class="mt-5">{{ $users->links() }}</div>
</x-layout.admin>
