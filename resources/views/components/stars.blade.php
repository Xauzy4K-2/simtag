@props(['value' => 0, 'size' => 'text-sm'])

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-0.5 $size"]) }} title="{{ $value }} dari 5">
    @for ($i = 1; $i <= 5; $i++)
        @if ($value >= $i)
            <i class="fa-solid fa-star text-amber-400"></i>
        @elseif ($value >= $i - 0.5)
            <i class="fa-solid fa-star-half-stroke text-amber-400"></i>
        @else
            <i class="fa-regular fa-star text-slate-300"></i>
        @endif
    @endfor
</span>
