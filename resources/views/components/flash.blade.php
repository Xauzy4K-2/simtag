@php
    $messages = collect([
        'success' => ['icon' => 'fa-circle-check', 'class' => 'border-emerald-200 bg-emerald-50 text-emerald-800'],
        'error' => ['icon' => 'fa-circle-exclamation', 'class' => 'border-rose-200 bg-rose-50 text-rose-800'],
        'info' => ['icon' => 'fa-circle-info', 'class' => 'border-sky-200 bg-sky-50 text-sky-800'],
    ])->filter(fn ($_, $key) => session()->has($key));
@endphp

@foreach ($messages as $key => $style)
    <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.300ms
         x-init="setTimeout(() => show = false, 7000)"
         class="mb-5 flex items-start gap-3 rounded-2xl border px-4 py-3 text-sm font-medium shadow-sm {{ $style['class'] }}" role="alert">
        <i class="fa-solid {{ $style['icon'] }} mt-0.5"></i>
        <p class="flex-1">{{ session($key) }}</p>
        <button type="button" @click="show = false" class="opacity-60 hover:opacity-100" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
    </div>
@endforeach

@if (session('credential'))
    <div class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 shadow-sm" x-data="{ copied: false }">
        <p class="font-bold"><i class="fa-solid fa-key mr-1.5"></i>Informasi login pelanggan</p>
        <p class="mt-1 text-amber-800">Simpan dan sampaikan kepada pelanggan. Kata sandi ini hanya ditampilkan sekali.</p>
        <div class="mt-3 grid gap-2 rounded-xl bg-white/70 p-3 font-mono text-xs sm:grid-cols-2">
            <span>Email: <b>{{ session('credential.email') }}</b></span>
            <span>Kata sandi: <b>{{ session('credential.password') }}</b></span>
        </div>
        <button type="button" class="btn btn-sm btn-secondary mt-3"
                @click="navigator.clipboard?.writeText({{ Js::from('Email: '.session('credential.email')."\nKata sandi: ".session('credential.password')) }}); copied = true; setTimeout(() => copied = false, 2000)">
            <i class="fa-regular" :class="copied ? 'fa-circle-check' : 'fa-copy'"></i><span x-text="copied ? 'Tersalin' : 'Salin'"></span>
        </button>
    </div>
@endif
