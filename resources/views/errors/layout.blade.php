<x-layout.base :title="$title" body-class="min-h-full bg-slate-50">
    <main class="flex min-h-screen flex-col items-center justify-center px-4 py-16 text-center">
        <a href="{{ route('home') }}"><x-logo size="lg" /></a>
        <p class="mt-10 bg-gradient-to-r from-brand-600 to-sky-600 bg-clip-text text-7xl font-extrabold text-transparent sm:text-8xl">{{ $code }}</p>
        <h1 class="mt-4 text-2xl font-extrabold text-slate-900">{{ $title }}</h1>
        <p class="mt-2 max-w-md text-slate-500">{{ $message }}</p>
        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i>Kembali</a>
            <a href="{{ auth()->check() ? auth()->user()->homeRoute() : route('home') }}" class="btn btn-primary"><i class="fa-solid fa-house"></i>{{ auth()->check() ? 'Ke Dashboard' : 'Ke Beranda' }}</a>
        </div>
    </main>
</x-layout.base>
