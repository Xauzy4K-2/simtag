<x-layout.admin :title="$customer->full_name">
    <x-page-header :title="$customer->full_name" :eyebrow="$customer->customer_number" :back="route('admin.customers.index')">
        <x-slot:actions>
            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-secondary"><i class="fa-solid fa-pen"></i>Ubah</a>
            @if ($customer->user)
                <form method="POST" action="{{ route('admin.customers.reset-password', $customer) }}" data-confirm="Buat kata sandi baru untuk pelanggan ini?">
                    @csrf
                    <button class="btn btn-secondary"><i class="fa-solid fa-key"></i>Reset Sandi</button>
                </form>
            @endif
            <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" data-confirm="Hapus pelanggan ini beserta seluruh jawaban survei dan pengaduannya? Tindakan ini tidak dapat dibatalkan.">
                @csrf @method('DELETE')
                <button class="btn btn-soft-danger"><i class="fa-solid fa-trash"></i>Hapus</button>
            </form>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <aside class="card card-body h-fit">
            <div class="flex items-center gap-4">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-sky-500 text-xl font-extrabold text-white">{{ $customer->initials }}</span>
                <div>
                    <x-status-badge :status="$customer->status" />
                    <p class="mt-1 text-xs text-slate-500">Terdaftar {{ $customer->created_at->translatedFormat('d M Y') }}</p>
                </div>
            </div>
            <dl class="mt-6 space-y-4 text-sm">
                @foreach ([
                    ['fa-envelope', 'Email', $customer->email],
                    ['fa-phone', 'Telepon', $customer->phone],
                    ['fa-venus-mars', 'Jenis Kelamin', $customer->gender_label],
                    ['fa-map', 'Kecamatan', $customer->district ?? '-'],
                    ['fa-location-dot', 'Alamat', $customer->address ?? '-'],
                    ['fa-clock', 'Login terakhir', $customer->user?->last_login_at?->translatedFormat('d M Y, H:i') ?? 'Belum pernah'],
                ] as [$icon, $label, $value])
                    <div class="flex gap-3">
                        <i class="fa-solid {{ $icon }} mt-1 w-4 text-center text-slate-400"></i>
                        <div class="min-w-0"><dt class="text-xs text-slate-400">{{ $label }}</dt><dd class="break-words font-medium text-slate-700">{{ $value }}</dd></div>
                    </div>
                @endforeach
            </dl>
        </aside>

        <div class="space-y-6 lg:col-span-2">
            <section class="card">
                <div class="card-header"><h2 class="card-title">Riwayat Survei <span class="badge badge-green ml-1">{{ $customer->surveyResponses->count() }}</span></h2></div>
                <div class="divide-y divide-slate-100">
                    @forelse ($customer->surveyResponses as $response)
                        <a href="{{ route('admin.surveys.responses.show', [$response->survey_id, $response]) }}" class="flex flex-col gap-2 px-4 py-3.5 transition hover:bg-slate-50 sm:flex-row sm:items-center sm:px-6">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-slate-800">{{ $response->survey?->title }}</p>
                                <p class="text-xs text-slate-500">{{ $response->submitted_at->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                            <x-score-badge :score="$response->average_score" />
                        </a>
                    @empty
                        <x-empty-state icon="fa-clipboard-list" title="Belum mengisi survei" />
                    @endforelse
                </div>
            </section>

            <section class="card">
                <div class="card-header"><h2 class="card-title">Riwayat Pengaduan <span class="badge badge-amber ml-1">{{ $customer->complaints->count() }}</span></h2></div>
                <div class="divide-y divide-slate-100">
                    @forelse ($customer->complaints as $complaint)
                        <a href="{{ route('admin.complaints.show', $complaint) }}" class="flex flex-col gap-2 px-4 py-3.5 transition hover:bg-slate-50 sm:flex-row sm:items-center sm:px-6">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-slate-800">{{ $complaint->subject }}</p>
                                <p class="text-xs text-slate-500"><span class="font-mono">{{ $complaint->ticket_number }}</span> · {{ $complaint->category }} · {{ $complaint->created_at->translatedFormat('d M Y') }}</p>
                            </div>
                            <x-status-badge :status="$complaint->status" />
                        </a>
                    @empty
                        <x-empty-state icon="fa-headset" title="Belum ada pengaduan" />
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-layout.admin>
