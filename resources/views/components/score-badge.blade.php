@props(['score' => null])

@php
    $label = \App\Models\SurveyResponse::satisfactionLabel($score);
    $class = match (true) {
        $score === null => 'badge-slate',
        $score >= 4.5 => 'badge-green',
        $score >= 3.5 => 'badge-blue',
        $score >= 2.5 => 'badge-amber',
        default => 'badge-red',
    };
@endphp

<span {{ $attributes->merge(['class' => "badge $class"]) }}>
    @if ($score !== null)
        <i class="fa-solid fa-star text-[9px]"></i>{{ number_format($score, 1, ',', '.') }} · {{ $label }}
    @else
        {{ $label }}
    @endif
</span>
