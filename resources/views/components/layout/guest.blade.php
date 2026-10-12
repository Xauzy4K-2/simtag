@props(['title' => null, 'heading' => null, 'subheading' => null])

<x-layout.base :title="$title" body-class="min-h-full bg-white">
    <div class="grid min-h-screen lg:grid-cols-2">
        {{-- Panel brand (desktop) --}}
        <aside class="relative hidden overflow-hidden bg-gradient-to-br from-brand-800 via-brand-700 to-sky-700 p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-20 h-[28rem] w-[28rem] rounded-full bg-sky-400/20 blur-3xl"></div>
            <img src="{{ asset('images/logo.png') }}" alt="" class="pointer-events-none absolute -bottom-10 -right-10 w-96 opacity-[0.07]">

            <a href="{{ route('home') }}" class="relative"><x-logo dark /></a>

            <div class="relative max-w-md">
                <p class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold ring-1 ring-white/20">
                    <i class="fa-solid fa-square-poll-vertical"></i> Sistem Informasi Survei
                </p>
                <h2 class="text-4xl font-extrabold leading-tight">Suara Anda menentukan kualitas air Pangkalpinang.</h2>
                <p class="mt-4 text-brand-100">Isi survei kepuasan, laporkan kendala layanan, dan pantau tindak lanjutnya dalam satu tempat.</p>
                <ul class="mt-8 space-y-3 text-sm text-brand-50">
                    <li class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10"><i class="fa-solid fa-star"></i></span>Survei kepuasan singkat & mudah</li>
                    <li class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10"><i class="fa-solid fa-headset"></i></span>Pengaduan dengan nomor tiket</li>
                    <li class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10"><i class="fa-solid fa-shield-halved"></i></span>Data pelanggan terlindungi</li>
                </ul>
            </div>

            <p class="relative text-xs text-brand-200">&copy; {{ date('Y') }} Perumda Air Minum Tirta Pinang Kota Pangkalpinang</p>
        </aside>

        {{-- Form --}}
        <main class="flex flex-col px-4 py-8 sm:px-8 lg:px-16">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="lg:hidden"><x-logo /></a>
                <a href="{{ route('home') }}" class="ml-auto inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-brand-700">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Beranda
                </a>
            </div>

            <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center py-10">
                @if ($heading)
                    <div class="mb-8">
                        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ $heading }}</h1>
                        @if ($subheading)
                            <p class="mt-2 text-sm text-slate-500">{{ $subheading }}</p>
                        @endif
                    </div>
                @endif

                <x-flash />
                {{ $slot }}
            </div>
        </main>
    </div>
</x-layout.base>
