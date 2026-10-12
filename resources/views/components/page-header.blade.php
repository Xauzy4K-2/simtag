@props(['title', 'subtitle' => null, 'back' => null, 'eyebrow' => null])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="min-w-0">
        @if ($back)
            <a href="{{ $back }}" class="mb-2 inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-brand-700">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali
            </a>
        @endif
        @if ($eyebrow)
            <p class="text-xs font-bold uppercase tracking-widest text-brand-600">{{ $eyebrow }}</p>
        @endif
        <h1 class="mt-0.5 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1.5 text-sm text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>
    @if (isset($actions))
        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endif
</div>
