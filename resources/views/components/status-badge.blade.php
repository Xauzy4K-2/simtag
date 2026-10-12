@props(['status', 'label' => null])

@php
    $map = [
        // pengaduan
        'open' => ['badge-amber', 'fa-inbox', 'Diterima'],
        'in_progress' => ['badge-blue', 'fa-spinner', 'Diproses'],
        'resolved' => ['badge-green', 'fa-circle-check', 'Selesai'],
        'closed' => ['badge-slate', 'fa-lock', 'Ditutup'],
        // survei
        'draft' => ['badge-slate', 'fa-pen', 'Draf'],
        'published' => ['badge-green', 'fa-circle-dot', 'Aktif'],
        'expired' => ['badge-amber', 'fa-hourglass-end', 'Berakhir'],
        // akun
        'active' => ['badge-green', 'fa-circle-check', 'Aktif'],
        'inactive' => ['badge-slate', 'fa-circle-pause', 'Nonaktif'],
        'blocked' => ['badge-red', 'fa-ban', 'Diblokir'],
        'suspended' => ['badge-red', 'fa-ban', 'Ditangguhkan'],
    ];
    [$class, $icon, $default] = $map[$status] ?? ['badge-slate', 'fa-circle', ucfirst((string) $status)];
@endphp

<span {{ $attributes->merge(['class' => "badge $class"]) }}>
    <i class="fa-solid {{ $icon }} text-[9px]"></i>{{ $label ?? $default }}
</span>
