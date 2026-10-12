@props(['title' => null])

@php
    $user = auth()->user();
    $menu = [
        ['route' => 'app.dashboard', 'match' => 'app.dashboard', 'icon' => 'fa-house', 'label' => 'Beranda'],
        ['route' => 'app.surveys.index', 'match' => 'app.surveys.*', 'icon' => 'fa-square-poll-horizontal', 'label' => 'Survei'],
        ['route' => 'app.complaints.index', 'match' => 'app.complaints.*', 'icon' => 'fa-comment-dots', 'label' => 'Pengaduan'],
        ['route' => 'app.history', 'match' => 'app.history', 'icon' => 'fa-clock-rotate-left', 'label' => 'Riwayat'],
        ['route' => 'app.profile', 'match' => 'app.profile', 'icon' => 'fa-user', 'label' => 'Profil'],
    ];
@endphp

<x-layout.base :title="$title" body-class="min-h-full bg-slate-50">
    <div x-data="{ drawer: false }" class="min-h-screen lg:flex">
        {{-- Sidebar desktop --}}
        <aside class="sticky top-0 hidden h-screen w-72 shrink-0 flex-col border-r border-slate-200 bg-white lg:flex">
            <div class="flex h-20 items-center border-b border-slate-100 px-6">
                <a href="{{ route('app.dashboard') }}"><x-logo /></a>
            </div>
            <nav class="flex-1 space-y-1 overflow-y-auto p-4">
                <p class="px-3 pb-2 text-[11px] font-bold uppercase tracking-widest text-slate-400">Menu Pelanggan</p>
                @foreach ($menu as $item)
                    <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs($item['match']) ? 'active' : '' }}">
                        <i class="fa-solid {{ $item['icon'] }}"></i>{{ $item['label'] }}
                    </a>
                @endforeach
                <a href="https://tirtapinang.co.id/" target="_blank" rel="noopener" class="nav-link">
                    <i class="fa-solid fa-globe"></i>Website PDAM <i class="fa-solid fa-arrow-up-right-from-square ml-auto text-[10px]"></i>
                </a>
            </nav>
            <div class="border-t border-slate-100 p-4">
                <div class="mb-3 flex items-center gap-3 rounded-xl bg-slate-50 p-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">{{ $user->initials }}</span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-slate-800">{{ $user->name }}</p>
                        <p class="truncate font-mono text-[11px] text-slate-500">{{ $user->customer?->customer_number }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-soft-danger w-full"><i class="fa-solid fa-right-from-bracket"></i>Keluar</button>
                </form>
            </div>
        </aside>

        {{-- Drawer mobile --}}
        <div x-cloak x-show="drawer" class="fixed inset-0 z-50 lg:hidden" @keydown.escape.window="drawer = false">
            <div x-show="drawer" x-transition.opacity class="absolute inset-0 bg-slate-900/50" @click="drawer = false"></div>
            <div x-show="drawer" x-transition:enter="transition duration-200" x-transition:enter-start="-translate-x-full" x-transition:leave="transition duration-200" x-transition:leave-end="-translate-x-full"
                 class="absolute inset-y-0 left-0 flex w-72 max-w-[85vw] flex-col bg-white shadow-2xl">
                <div class="flex h-16 items-center justify-between border-b px-5">
                    <x-logo size="sm" />
                    <button @click="drawer = false" class="btn-ghost rounded-lg p-2" aria-label="Tutup menu"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>
                <nav class="flex-1 space-y-1 overflow-y-auto p-4">
                    @foreach ($menu as $item)
                        <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs($item['match']) ? 'active' : '' }}">
                            <i class="fa-solid {{ $item['icon'] }}"></i>{{ $item['label'] }}
                        </a>
                    @endforeach
                    <a href="https://tirtapinang.co.id/" target="_blank" rel="noopener" class="nav-link"><i class="fa-solid fa-globe"></i>Website PDAM</a>
                </nav>
                <form method="POST" action="{{ route('logout') }}" class="border-t p-4">
                    @csrf
                    <button class="btn btn-soft-danger w-full"><i class="fa-solid fa-right-from-bracket"></i>Keluar Aplikasi</button>
                </form>
            </div>
        </div>

        <div class="flex min-w-0 flex-1 flex-col">
            {{-- Topbar mobile --}}
            <header class="sticky top-0 z-40 flex h-16 items-center justify-between border-b border-slate-200 bg-white/90 px-4 backdrop-blur lg:hidden">
                <button @click="drawer = true" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100" aria-label="Buka menu"><i class="fa-solid fa-bars text-lg"></i></button>
                <a href="{{ route('app.dashboard') }}"><x-logo size="sm" /></a>
                <a href="{{ route('app.profile') }}" class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700" aria-label="Profil">{{ $user->initials }}</a>
            </header>

            <main class="relative mx-auto w-full max-w-6xl flex-1 px-4 pb-28 pt-6 sm:px-6 lg:px-10 lg:pb-12 lg:pt-10">
                <img src="{{ asset('images/logo.png') }}" alt="" aria-hidden="true" class="pointer-events-none fixed bottom-24 right-0 -z-0 w-72 opacity-[0.03] lg:w-96">
                <div class="relative">
                    <x-flash />
                    {{ $slot }}
                </div>
            </main>
        </div>

        {{-- Bottom navigation mobile --}}
        <nav class="fixed inset-x-0 bottom-0 z-40 grid grid-cols-5 border-t border-slate-200 bg-white/95 pb-[env(safe-area-inset-bottom)] backdrop-blur lg:hidden">
            @foreach ($menu as $item)
                @php $active = request()->routeIs($item['match']); @endphp
                <a href="{{ route($item['route']) }}" class="flex flex-col items-center gap-1 py-2.5 text-[10px] font-semibold {{ $active ? 'text-brand-600' : 'text-slate-400' }}">
                    <span class="flex h-7 w-12 items-center justify-center rounded-full {{ $active ? 'bg-brand-50' : '' }}"><i class="fa-solid {{ $item['icon'] }} text-base"></i></span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</x-layout.base>
