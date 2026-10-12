@props(['title' => null])

@php
    $user = auth()->user();
    $openComplaints = \App\Models\Complaint::where('status', 'open')->count();
    $groups = [
        'Utama' => [
            ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'icon' => 'fa-chart-pie', 'label' => 'Ringkasan'],
            ['route' => 'admin.surveys.index', 'match' => 'admin.surveys.*', 'icon' => 'fa-square-poll-vertical', 'label' => 'Survei'],
            ['route' => 'admin.complaints.index', 'match' => 'admin.complaints.*', 'icon' => 'fa-triangle-exclamation', 'label' => 'Pengaduan', 'count' => $openComplaints],
            ['route' => 'admin.customers.index', 'match' => 'admin.customers.*', 'icon' => 'fa-users', 'label' => 'Pelanggan'],
            ['route' => 'admin.reports.index', 'match' => 'admin.reports.*', 'icon' => 'fa-file-lines', 'label' => 'Laporan Kepuasan'],
        ],
    ];
    if ($user->isAdmin()) {
        $groups['Administrasi'] = [
            ['route' => 'admin.users.index', 'match' => 'admin.users.*', 'icon' => 'fa-user-shield', 'label' => 'Pengguna Sistem'],
            ['route' => 'admin.logs.index', 'match' => 'admin.logs.*', 'icon' => 'fa-list-check', 'label' => 'Log Aktivitas'],
        ];
    }
    $groups['Akun'] = [
        ['route' => 'admin.profile', 'match' => 'admin.profile', 'icon' => 'fa-user-gear', 'label' => 'Profil Saya'],
    ];
@endphp

<x-layout.base :title="$title" body-class="min-h-full bg-slate-100/70">
    <div x-data="{ drawer: false }" class="min-h-screen lg:flex">
        {{-- Sidebar (drawer di mobile, statis di desktop) --}}
        <div x-cloak x-show="drawer" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/60 lg:hidden" @click="drawer = false"></div>
        <aside :class="{ 'max-lg:translate-x-0!': drawer }"
               class="fixed inset-y-0 left-0 z-50 flex w-72 max-w-[85vw] shrink-0 flex-col bg-slate-900 text-slate-300 transition-transform duration-200 max-lg:-translate-x-full lg:sticky lg:top-0 lg:h-screen">
            <div class="flex h-20 items-center justify-between border-b border-white/5 px-6">
                <a href="{{ route('admin.dashboard') }}"><x-logo dark /></a>
                <button @click="drawer = false" class="rounded-lg p-2 text-slate-400 hover:bg-white/5 lg:hidden" aria-label="Tutup menu"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-6">
                @foreach ($groups as $group => $items)
                    <div>
                        <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">{{ $group }}</p>
                        <div class="space-y-1">
                            @foreach ($items as $item)
                                @php $active = request()->routeIs($item['match']); @endphp
                                <a href="{{ route($item['route']) }}"
                                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $active ? 'bg-brand-600 text-white shadow-lg shadow-brand-900/40' : 'hover:bg-white/5 hover:text-white' }}">
                                    <i class="fa-solid {{ $item['icon'] }} w-5 text-center {{ $active ? '' : 'text-slate-500' }}"></i>
                                    <span class="flex-1">{{ $item['label'] }}</span>
                                    @if (! empty($item['count']))
                                        <span class="rounded-full bg-amber-400 px-2 py-0.5 text-[10px] font-extrabold text-slate-900">{{ $item['count'] }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>

            <div class="border-t border-white/5 p-4">
                <div class="flex items-center gap-3 rounded-xl bg-white/5 p-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-500/20 text-sm font-bold text-brand-300">{{ $user->initials }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold text-white">{{ $user->name }}</p>
                        <p class="text-[11px] text-slate-400">{{ $user->role_label }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg p-2 text-slate-400 hover:bg-white/10 hover:text-rose-300" title="Keluar" aria-label="Keluar"><i class="fa-solid fa-right-from-bracket"></i></button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:h-20 lg:px-10">
                <button @click="drawer = true" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden" aria-label="Buka menu"><i class="fa-solid fa-bars text-lg"></i></button>
                <div class="min-w-0 flex-1">
                    <p class="hidden text-xs font-semibold text-slate-400 sm:block">{{ now()->translatedFormat('l, d F Y') }}</p>
                    <p class="truncate text-sm font-bold text-slate-800 sm:text-base">Panel {{ $user->role_label }}</p>
                </div>
                <a href="{{ route('admin.complaints.index', ['status' => 'open']) }}" class="relative rounded-xl p-2.5 text-slate-500 hover:bg-slate-100" title="Pengaduan baru">
                    <i class="fa-solid fa-bell"></i>
                    @if ($openComplaints)
                        <span class="absolute right-1 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] font-bold text-white">{{ $openComplaints > 99 ? '99+' : $openComplaints }}</span>
                    @endif
                </a>
                <a href="{{ route('home') }}" target="_blank" class="btn btn-secondary btn-sm hidden sm:inline-flex"><i class="fa-solid fa-globe"></i>Lihat Situs</a>
            </header>

            <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6 lg:px-10 lg:py-8">
                <x-flash />
                {{ $slot }}
            </main>

            <footer class="px-4 pb-6 text-center text-xs text-slate-400 sm:px-6 lg:px-10">
                SIMTAG &middot; Perumda Air Minum Tirta Pinang Kota Pangkalpinang &copy; {{ date('Y') }}
            </footer>
        </div>
    </div>
</x-layout.base>
