@props(['dark' => false, 'size' => 'md'])

@php
    $img = ['sm' => 'h-8 w-8', 'md' => 'h-10 w-10', 'lg' => 'h-14 w-14'][$size] ?? 'h-10 w-10';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <img src="{{ asset('images/logo.png') }}" alt="Logo PDAM Tirta Pinang" class="{{ $img }} object-contain">
    <span class="leading-none">
        <span class="block text-[9px] font-bold tracking-[0.25em] {{ $dark ? 'text-brand-300' : 'text-slate-400' }}">PDAM KOTA</span>
        <span class="mt-1 block text-sm font-extrabold tracking-wider {{ $dark ? 'text-white' : 'text-brand-700' }}">TIRTA PINANG</span>
    </span>
</span>
