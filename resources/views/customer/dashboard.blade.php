<x-layout.customer title="Beranda">
    @php
        $hour = now()->hour;
        $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
    @endphp

    {{-- Sapaan --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm text-slate-500">{{ $greeting }},</p>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ $customer->full_name }} 👋</h1>
            <p class="mt-1 font-mono text-xs text-slate-400">No. Pelanggan: {{ $customer->customer_number }}{{ $customer->district ? ' • Kec. '.$customer->district : '' }}</p>
        </div>
        <x-status-badge :status="$customer->status" />
    </div>

    {{-- Kartu ajakan survei --}}
    @if ($pendingSurveys->isNotEmpty())
        @php $next = $pendingSurveys->first(); @endphp
        <a href="{{ route('app.surveys.show', $next) }}" class="group relative mb-6 block overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 to-sky-700 p-5 text-white shadow-lg shadow-brand-600/20 sm:p-7">
            <i class="fa-solid fa-square-poll-horizontal absolute -bottom-6 -right-4 text-[8rem] text-white/10 transition group-hover:scale-110"></i>
            <span class="badge bg-white/15 text-white ring-1 ring-white/20"><i class="fa-solid fa-bell text-[9px]"></i>{{ $stats['pending'] }} survei menunggu Anda</span>
            <h2 class="relative mt-3 max-w-xl text-xl font-extrabold sm:text-2xl">{{ $next->title }}</h2>
            <p class="relative mt-1 max-w-xl text-sm text-brand-50 line-clamp-2">{{ $next->description }}</p>
            <div class="relative mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-brand-50">
                <span><i class="fa-regular fa-circle-question mr-1"></i>{{ $next->questions_count }} pertanyaan</span>
                <span><i class="fa-regular fa-clock mr-1"></i>± {{ max(1, ceil($next->questions_count * 0.25)) }} menit</span>
                @if ($next->ends_at)<span><i class="fa-regular fa-calendar mr-1"></i>Hingga {{ $next->ends_at->translatedFormat('d M Y') }}</span>@endif
            </div>
            <span class="relative mt-5 inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-brand-700">Isi Survei Sekarang <i class="fa-solid fa-arrow-right"></i></span>
        </a>
    @else
        <div class="mb-6 flex items-center gap-4 rounded-3xl border border-brand-100 bg-brand-50 p-5">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white text-xl text-brand-600"><i class="fa-solid fa-circle-check"></i></span>
            <div>
                <p class="font-bold text-brand-800">Terima kasih! Semua survei sudah Anda isi.</p>
                <p class="text-sm text-brand-700">Kami akan memberi tahu jika ada survei baru.</p>
            </div>
        </div>
    @endif

    {{-- Statistik ringkas --}}
    <div class="mb-6 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        @foreach ([
            ['Survei Menunggu', $stats['pending'], 'fa-hourglass-half', 'bg-amber-50 text-amber-600', route('app.surveys.index')],
            ['Survei Diisi', $stats['completed'], 'fa-clipboard-check', 'bg-brand-50 text-brand-600', route('app.surveys.index')],
            ['Pengaduan Aktif', $stats['activeComplaints'], 'fa-screwdriver-wrench', 'bg-sky-50 text-sky-600', route('app.complaints.index')],
            ['Total Pengaduan', $stats['totalComplaints'], 'fa-comments', 'bg-violet-50 text-violet-600', route('app.complaints.index')],
        ] as [$label, $value, $icon, $color, $url])
            <a href="{{ $url }}" class="card flex items-center gap-3 p-4 transition hover:-translate-y-0.5 hover:shadow-md">
                <span class="stat-icon {{ $color }}"><i class="fa-solid {{ $icon }}"></i></span>
                <div class="min-w-0">
                    <p class="text-2xl font-extrabold leading-none text-slate-900">{{ $value }}</p>
                    <p class="mt-1 truncate text-xs font-medium text-slate-500">{{ $label }}</p>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Aksi cepat --}}
    <div class="card mb-6 grid grid-cols-4 gap-1 p-2 sm:gap-2 sm:p-3">
        @foreach ([
            ['Isi Survei', 'fa-star-half-stroke', 'bg-amber-50 text-amber-500', route('app.surveys.index')],
            ['Lapor Gangguan', 'fa-triangle-exclamation', 'bg-rose-50 text-rose-500', route('app.complaints.create')],
            ['Riwayat', 'fa-clock-rotate-left', 'bg-violet-50 text-violet-600', route('app.history')],
            ['Profil', 'fa-user-gear', 'bg-sky-50 text-sky-600', route('app.profile')],
        ] as [$label, $icon, $color, $url])
            <a href="{{ $url }}" class="flex flex-col items-center gap-2 rounded-xl p-2 text-center transition hover:bg-slate-50 sm:p-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $color }}"><i class="fa-solid {{ $icon }}"></i></span>
                <span class="text-[11px] font-semibold leading-tight text-slate-700 sm:text-xs">{{ $label }}</span>
            </a>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Survei terakhir --}}
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Survei Terakhir Diisi</h2>
                <a href="{{ route('app.surveys.index') }}" class="text-sm font-semibold text-brand-600 hover:underline">Semua</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentResponses as $response)
                    <a href="{{ route('app.surveys.show', $response->survey_id) }}" class="flex items-center gap-3 px-4 py-3.5 transition hover:bg-slate-50 sm:px-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><i class="fa-solid fa-clipboard-check"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $response->survey?->title }}</p>
                            <p class="text-xs text-slate-500">{{ $response->submitted_at->translatedFormat('d M Y, H:i') }}</p>
                        </div>
                        @if ($response->average_score)<x-stars :value="$response->average_score" size="text-[11px]" class="hidden sm:inline-flex" />@endif
                    </a>
                @empty
                    <x-empty-state icon="fa-clipboard-list" title="Belum ada survei yang diisi" description="Jawaban survei Anda akan tampil di sini." />
                @endforelse
            </div>
        </section>

        {{-- Pengaduan terakhir --}}
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Status Pengaduan</h2>
                <a href="{{ route('app.complaints.index') }}" class="text-sm font-semibold text-brand-600 hover:underline">Semua</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentComplaints as $complaint)
                    <a href="{{ route('app.complaints.show', $complaint) }}" class="flex items-center gap-3 px-4 py-3.5 transition hover:bg-slate-50 sm:px-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-500"><i class="fa-solid fa-comment-dots"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $complaint->subject }}</p>
                            <p class="truncate font-mono text-[11px] text-slate-500">{{ $complaint->ticket_number }} · {{ $complaint->created_at->diffForHumans() }}</p>
                        </div>
                        <x-status-badge :status="$complaint->status" />
                    </a>
                @empty
                    <x-empty-state icon="fa-headset" title="Belum ada pengaduan" description="Ada kendala air? Laporkan kepada kami.">
                        <a href="{{ route('app.complaints.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i>Buat Pengaduan</a>
                    </x-empty-state>
                @endforelse
            </div>
        </section>
    </div>

    {{-- Banner pengaduan --}}
    <a href="{{ route('app.complaints.create') }}" class="mt-6 flex items-center gap-4 rounded-2xl border border-l-4 border-slate-200 border-l-rose-500 bg-white p-4 transition hover:bg-rose-50/40">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-500"><i class="fa-solid fa-circle-exclamation"></i></span>
        <div class="flex-1">
            <p class="text-sm font-bold text-rose-600">Pusat Laporan Kendala</p>
            <p class="text-xs text-slate-500">Air mati mendadak atau ada kebocoran pipa? Kirim pengaduan sekarang.</p>
        </div>
        <i class="fa-solid fa-chevron-right text-slate-300"></i>
    </a>
</x-layout.customer>
