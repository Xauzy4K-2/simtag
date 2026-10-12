<x-layout.base title="Survei Kepuasan Pelanggan" body-class="min-h-full bg-white">
    {{-- ================= NAVBAR ================= --}}
    <header x-data="{ open: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 10"
            :class="scrolled ? 'bg-white/90 shadow-sm backdrop-blur' : 'bg-transparent'"
            class="fixed inset-x-0 top-0 z-50 transition">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:h-20 lg:px-8">
            <a href="{{ route('home') }}"><x-logo /></a>

            <nav class="hidden items-center gap-1 lg:flex">
                @foreach (['#tentang' => 'Tentang', '#fitur' => 'Fitur', '#survei' => 'Survei Aktif', '#cara-kerja' => 'Cara Kerja', '#faq' => 'FAQ'] as $href => $label)
                    <a href="{{ $href }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-brand-50 hover:text-brand-700">{{ $label }}</a>
                @endforeach
            </nav>

            <div class="hidden items-center gap-2 lg:flex">
                @auth
                    <a href="{{ auth()->user()->homeRoute() }}" class="btn btn-primary"><i class="fa-solid fa-gauge"></i>Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Daftar Sekarang</a>
                @endauth
            </div>

            <button @click="open = !open" class="rounded-lg p-2 text-slate-700 hover:bg-slate-100 lg:hidden" aria-label="Menu">
                <i class="fa-solid text-xl" :class="open ? 'fa-xmark' : 'fa-bars'"></i>
            </button>
        </div>

        <div x-cloak x-show="open" x-collapse class="border-t border-slate-100 bg-white shadow-lg lg:hidden">
            <nav class="space-y-1 px-4 py-4">
                @foreach (['#tentang' => 'Tentang', '#fitur' => 'Fitur', '#survei' => 'Survei Aktif', '#cara-kerja' => 'Cara Kerja', '#faq' => 'FAQ'] as $href => $label)
                    <a href="{{ $href }}" @click="open = false" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-brand-50">{{ $label }}</a>
                @endforeach
                <div class="grid grid-cols-2 gap-2 pt-3">
                    @auth
                        <a href="{{ auth()->user()->homeRoute() }}" class="btn btn-primary col-span-2">Buka Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
                    @endauth
                </div>
            </nav>
        </div>
    </header>

    <main>
        {{-- ================= HERO ================= --}}
        <section class="relative overflow-hidden bg-gradient-to-b from-brand-50 via-white to-white pt-28 pb-16 sm:pt-32 lg:pt-40 lg:pb-24">
            <div class="pointer-events-none absolute -right-40 -top-40 h-[32rem] w-[32rem] rounded-full bg-sky-200/40 blur-3xl"></div>
            <div class="pointer-events-none absolute -left-32 top-40 h-80 w-80 rounded-full bg-brand-200/40 blur-3xl"></div>

            <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
                <div class="text-center lg:text-left">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1.5 text-xs font-bold text-brand-700 shadow-sm ring-1 ring-brand-100">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-brand-500"></span> SIMTAG · Sistem Informasi Survei Pelanggan
                    </span>
                    <h1 class="mt-6 text-4xl font-extrabold leading-[1.1] tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
                        Suara Anda,<br class="hidden sm:block">
                        <span class="bg-gradient-to-r from-brand-600 to-sky-600 bg-clip-text text-transparent">air bersih lebih baik</span>
                        untuk Pangkalpinang.
                    </h1>
                    <p class="mx-auto mt-6 max-w-xl text-base text-slate-600 sm:text-lg lg:mx-0">
                        Platform resmi PDAM Tirta Pinang untuk mengukur kepuasan pelanggan. Isi survei, sampaikan masukan, dan laporkan kendala layanan air &mdash; semuanya online, kapan saja.
                    </p>
                    <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row lg:justify-start">
                        @auth
                            <a href="{{ auth()->user()->homeRoute() }}" class="btn btn-primary btn-lg"><i class="fa-solid fa-gauge"></i>Buka Dashboard</a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg"><i class="fa-solid fa-user-plus"></i>Daftar & Isi Survei</a>
                            <a href="{{ route('login') }}" class="btn btn-secondary btn-lg"><i class="fa-solid fa-right-to-bracket"></i>Masuk</a>
                        @endauth
                    </div>
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-slate-500 lg:justify-start">
                        <span><i class="fa-solid fa-circle-check mr-1.5 text-brand-500"></i>Gratis</span>
                        <span><i class="fa-solid fa-circle-check mr-1.5 text-brand-500"></i>± 3 menit</span>
                        <span><i class="fa-solid fa-circle-check mr-1.5 text-brand-500"></i>Data terlindungi</span>
                    </div>
                </div>

                {{-- Visual kartu survei --}}
                <div class="relative mx-auto w-full max-w-md lg:max-w-none">
                    <div class="absolute inset-0 -rotate-3 rounded-[2rem] bg-gradient-to-br from-brand-500 to-sky-500 opacity-90"></div>
                    <div class="relative rounded-[2rem] bg-white p-5 shadow-2xl ring-1 ring-slate-100 sm:p-7">
                        <div class="flex items-center justify-between">
                            <x-logo size="sm" />
                            <span class="badge badge-green"><i class="fa-solid fa-circle-dot text-[8px]"></i>Survei Aktif</span>
                        </div>
                        <p class="mt-5 text-xs font-bold uppercase tracking-wider text-slate-400">Pertanyaan 1 dari 12</p>
                        <p class="mt-1 font-bold text-slate-800">Bagaimana kualitas kejernihan air bersih di rumah Anda?</p>
                        <div class="mt-3 flex gap-1.5 text-2xl">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star {{ $i <= 4 ? 'text-amber-400' : 'text-slate-200' }}"></i>
                            @endfor
                        </div>
                        <div class="mt-5 h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full w-2/3 rounded-full bg-gradient-to-r from-brand-500 to-sky-500"></div></div>
                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-brand-50 p-4">
                                <p class="text-[11px] font-bold uppercase tracking-wide text-brand-700">Indeks Kepuasan</p>
                                <p class="mt-1 text-3xl font-extrabold text-brand-800">{{ $stats['index'] !== null ? number_format($stats['index'], 1, ',', '.') : '–' }}</p>
                                <p class="text-xs text-brand-700">{{ $stats['label'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-sky-50 p-4">
                                <p class="text-[11px] font-bold uppercase tracking-wide text-sky-700">Total Respons</p>
                                <p class="mt-1 text-3xl font-extrabold text-sky-800">{{ number_format($stats['responses'], 0, ',', '.') }}</p>
                                <p class="text-xs text-sky-700">jawaban survei</p>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -bottom-6 -left-2 hidden items-center gap-3 rounded-2xl bg-white p-3 pr-5 shadow-xl ring-1 ring-slate-100 sm:flex">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600"><i class="fa-solid fa-headset"></i></span>
                        <div><p class="text-xs text-slate-500">Pengaduan terselesaikan</p><p class="font-extrabold text-slate-900">{{ number_format($stats['resolved'], 0, ',', '.') }} tiket</p></div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= STATISTIK ================= --}}
        <section class="border-y border-slate-100 bg-white">
            <div class="mx-auto grid max-w-7xl grid-cols-2 gap-px bg-slate-100 lg:grid-cols-4">
                @foreach ([
                    ['fa-users', number_format($stats['customers'], 0, ',', '.'), 'Pelanggan terdaftar'],
                    ['fa-square-poll-vertical', number_format($stats['responses'], 0, ',', '.'), 'Respons survei'],
                    ['fa-face-smile', $stats['index'] !== null ? number_format($stats['index'], 1, ',', '.').'%' : '–', 'Indeks kepuasan'],
                    ['fa-screwdriver-wrench', number_format($stats['resolved'], 0, ',', '.'), 'Pengaduan ditangani'],
                ] as [$icon, $value, $label])
                    <div class="bg-white px-4 py-8 text-center sm:px-6">
                        <i class="fa-solid {{ $icon }} text-xl text-brand-500"></i>
                        <p class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ $value }}</p>
                        <p class="text-xs font-medium text-slate-500 sm:text-sm">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ================= TENTANG ================= --}}
        <section id="tentang" class="scroll-mt-20 py-16 sm:py-24">
            <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
                <div class="order-2 grid grid-cols-2 gap-4 lg:order-1">
                    <div class="space-y-4">
                        <div class="rounded-3xl bg-brand-600 p-6 text-white"><i class="fa-solid fa-droplet text-3xl"></i><p class="mt-6 text-lg font-bold">Kualitas Air</p><p class="text-sm text-brand-100">Kejernihan, bau, dan rasa air.</p></div>
                        <div class="rounded-3xl bg-slate-900 p-6 text-white"><i class="fa-solid fa-gauge-high text-3xl text-sky-300"></i><p class="mt-6 text-lg font-bold">Kontinuitas</p><p class="text-sm text-slate-400">Tekanan & aliran 24 jam.</p></div>
                    </div>
                    <div class="mt-8 space-y-4">
                        <div class="rounded-3xl bg-sky-100 p-6 text-sky-900"><i class="fa-solid fa-people-group text-3xl"></i><p class="mt-6 text-lg font-bold">Pelayanan</p><p class="text-sm text-sky-700">Sikap & respon petugas.</p></div>
                        <div class="rounded-3xl bg-amber-100 p-6 text-amber-900"><i class="fa-solid fa-bullhorn text-3xl"></i><p class="mt-6 text-lg font-bold">Informasi</p><p class="text-sm text-amber-700">Pemberitahuan gangguan.</p></div>
                    </div>
                </div>
                <div class="order-1 lg:order-2">
                    <p class="text-sm font-bold uppercase tracking-widest text-brand-600">Tentang SIMTAG</p>
                    <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Mengukur kepuasan, mewujudkan layanan yang lebih baik.</h2>
                    <p class="mt-5 text-slate-600">SIMTAG adalah sistem informasi survei milik Perumda Air Minum Tirta Pinang Kota Pangkalpinang. Melalui SIMTAG, setiap pelanggan dapat memberikan penilaian secara langsung terhadap kualitas air dan pelayanan yang diterima.</p>
                    <p class="mt-4 text-slate-600">Hasil survei diolah menjadi <b>Indeks Kepuasan Pelanggan</b> per aspek layanan dan per kecamatan, sehingga PDAM dapat menentukan prioritas perbaikan secara tepat sasaran.</p>
                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div class="flex gap-3"><i class="fa-solid fa-circle-check mt-1 text-brand-500"></i><p class="text-sm text-slate-600"><b class="text-slate-800">Transparan.</b> Setiap pengaduan memiliki nomor tiket & status.</p></div>
                        <div class="flex gap-3"><i class="fa-solid fa-circle-check mt-1 text-brand-500"></i><p class="text-sm text-slate-600"><b class="text-slate-800">Terukur.</b> Skala penilaian 1–5 yang konsisten.</p></div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= FITUR ================= --}}
        <section id="fitur" class="scroll-mt-20 bg-slate-50 py-16 sm:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-sm font-bold uppercase tracking-widest text-brand-600">Fitur Utama</p>
                    <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Semua yang dibutuhkan untuk mendengar pelanggan</h2>
                </div>
                <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ([
                        ['fa-star-half-stroke', 'bg-amber-100 text-amber-600', 'Survei Kepuasan', 'Pertanyaan rating bintang, pilihan ganda, dan isian bebas yang mudah diisi dari HP.'],
                        ['fa-comment-dots', 'bg-rose-100 text-rose-600', 'Pusat Pengaduan', 'Laporkan air mati, pipa bocor, atau air keruh lengkap dengan foto dan lokasi.'],
                        ['fa-clock-rotate-left', 'bg-violet-100 text-violet-600', 'Riwayat Aktivitas', 'Pantau survei yang telah diisi dan perkembangan setiap pengaduan Anda.'],
                        ['fa-chart-line', 'bg-sky-100 text-sky-600', 'Analitik Real-time', 'Grafik tren kepuasan, sebaran per kecamatan, dan aspek yang perlu perbaikan.'],
                        ['fa-file-excel', 'bg-emerald-100 text-emerald-600', 'Laporan & Ekspor', 'Ekspor hasil survei ke Excel dan cetak laporan resmi dalam satu klik.'],
                        ['fa-mobile-screen', 'bg-slate-200 text-slate-700', 'Responsif', 'Nyaman diakses dari ponsel, tablet, maupun komputer tanpa instal aplikasi.'],
                    ] as [$icon, $color, $title, $desc])
                        <div class="group rounded-3xl border border-slate-200/70 bg-white p-6 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-200/60">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl text-xl {{ $color }}"><i class="fa-solid {{ $icon }}"></i></span>
                            <h3 class="mt-5 text-lg font-bold text-slate-900">{{ $title }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $desc }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ================= SURVEI AKTIF ================= --}}
        <section id="survei" class="scroll-mt-20 py-16 sm:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-widest text-brand-600">Survei Aktif</p>
                        <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Survei yang sedang berlangsung</h2>
                    </div>
                    <a href="{{ auth()->check() ? auth()->user()->homeRoute() : route('login') }}" class="btn btn-secondary">Lihat semua <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($activeSurveys as $survey)
                        <article class="flex flex-col rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="badge badge-green"><i class="fa-solid fa-circle-dot text-[8px]"></i>Dibuka</span>
                                <span class="text-xs font-semibold text-slate-400"><i class="fa-regular fa-circle-question mr-1"></i>{{ $survey->questions_count }} pertanyaan</span>
                            </div>
                            <h3 class="mt-4 text-lg font-bold text-slate-900">{{ $survey->title }}</h3>
                            <p class="mt-2 line-clamp-3 flex-1 text-sm text-slate-600">{{ $survey->description }}</p>
                            <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                                <span class="text-xs text-slate-500">
                                    <i class="fa-regular fa-calendar mr-1"></i>{{ $survey->ends_at ? 'Hingga '.$survey->ends_at->translatedFormat('d M Y') : 'Tanpa batas waktu' }}
                                </span>
                                <a href="{{ auth()->check() && auth()->user()->isCustomer() ? route('app.surveys.show', $survey) : route('login') }}" class="text-sm font-bold text-brand-600 hover:text-brand-700">Isi survei <i class="fa-solid fa-arrow-right text-xs"></i></a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-3xl border border-dashed border-slate-300 md:col-span-2 lg:col-span-3">
                            <x-empty-state icon="fa-square-poll-horizontal" title="Belum ada survei aktif" description="Nantikan survei berikutnya dari PDAM Tirta Pinang." />
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- ================= CARA KERJA ================= --}}
        <section id="cara-kerja" class="scroll-mt-20 bg-slate-900 py-16 text-white sm:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-sm font-bold uppercase tracking-widest text-brand-400">Cara Kerja</p>
                    <h2 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">Empat langkah mudah</h2>
                </div>
                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['fa-user-plus', 'Daftar Akun', 'Buat akun pelanggan dengan email dan data alamat Anda.'],
                        ['fa-right-to-bracket', 'Masuk', 'Login memakai email atau nomor pelanggan Anda.'],
                        ['fa-list-check', 'Isi Survei', 'Beri penilaian 1–5 bintang dan tuliskan saran Anda.'],
                        ['fa-chart-simple', 'Ditindaklanjuti', 'PDAM menganalisis hasil untuk perbaikan layanan.'],
                    ] as $i => [$icon, $title, $desc])
                        <div class="relative rounded-3xl bg-white/5 p-6 ring-1 ring-white/10">
                            <span class="absolute right-5 top-5 text-5xl font-extrabold text-white/5">0{{ $i + 1 }}</span>
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-500/20 text-xl text-brand-300"><i class="fa-solid {{ $icon }}"></i></span>
                            <h3 class="mt-5 text-lg font-bold">{{ $title }}</h3>
                            <p class="mt-2 text-sm text-slate-400">{{ $desc }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ================= FAQ ================= --}}
        <section id="faq" class="scroll-mt-20 py-16 sm:py-24">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <p class="text-sm font-bold uppercase tracking-widest text-brand-600">FAQ</p>
                    <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Pertanyaan yang sering diajukan</h2>
                </div>
                <div class="mt-10 space-y-3" x-data="{ active: 0 }">
                    @foreach ([
                        ['Siapa saja yang dapat mengisi survei?', 'Seluruh pelanggan PDAM Tirta Pinang Kota Pangkalpinang yang telah memiliki akun SIMTAG. Pendaftaran gratis dan dapat dilakukan secara mandiri.'],
                        ['Apakah saya bisa mengisi survei yang sama lebih dari sekali?', 'Tidak. Setiap pelanggan hanya dapat mengisi satu kali per survei agar hasilnya akurat. Anda tetap dapat melihat kembali jawaban yang telah dikirim.'],
                        ['Bagaimana cara melaporkan gangguan air?', 'Masuk ke akun Anda, buka menu Pengaduan, pilih kategori masalah, jelaskan kendala, lalu lampirkan foto bila ada. Anda akan menerima nomor tiket untuk memantau prosesnya.'],
                        ['Apakah data saya aman?', 'Ya. Data pribadi hanya digunakan untuk keperluan pelayanan PDAM Tirta Pinang dan tidak dipublikasikan. Hasil survei ditampilkan dalam bentuk agregat.'],
                        ['Saya lupa kata sandi, apa yang harus dilakukan?', 'Gunakan fitur "Lupa kata sandi" di halaman masuk. Kode OTP akan dikirim ke email terdaftar untuk membuat kata sandi baru.'],
                    ] as $i => [$q, $a])
                        <div class="rounded-2xl border border-slate-200 bg-white">
                            <button type="button" @click="active = active === {{ $i }} ? null : {{ $i }}" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                                <span class="font-bold text-slate-800">{{ $q }}</span>
                                <i class="fa-solid fa-chevron-down text-sm text-slate-400 transition" :class="active === {{ $i }} && 'rotate-180 text-brand-600'"></i>
                            </button>
                            <div x-show="active === {{ $i }}" x-collapse {{ $i === 0 ? '' : 'x-cloak' }}>
                                <p class="px-5 pb-5 text-sm leading-relaxed text-slate-600">{{ $a }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ================= CTA ================= --}}
        <section class="px-4 pb-16 sm:px-6 sm:pb-24 lg:px-8">
            <div class="relative mx-auto max-w-7xl overflow-hidden rounded-[2rem] bg-gradient-to-br from-brand-600 to-sky-700 px-6 py-12 text-center text-white sm:px-12 sm:py-16">
                <img src="{{ asset('images/logo.png') }}" alt="" class="pointer-events-none absolute -right-10 -top-10 w-64 opacity-10">
                <h2 class="relative text-3xl font-extrabold tracking-tight sm:text-4xl">Bantu kami melayani lebih baik</h2>
                <p class="relative mx-auto mt-3 max-w-xl text-brand-50">Luangkan 3 menit untuk mengisi survei. Setiap jawaban Anda menjadi dasar perbaikan layanan air bersih di Kota Pangkalpinang.</p>
                <div class="relative mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-lg bg-white text-brand-700 hover:bg-brand-50">Daftar Gratis</a>
                        <a href="{{ route('login') }}" class="btn btn-lg bg-white/10 text-white ring-1 ring-white/30 hover:bg-white/20">Saya sudah punya akun</a>
                    @else
                        <a href="{{ auth()->user()->homeRoute() }}" class="btn btn-lg bg-white text-brand-700 hover:bg-brand-50">Buka Dashboard</a>
                    @endguest
                </div>
            </div>
        </section>
    </main>

    {{-- ================= FOOTER ================= --}}
    <footer class="border-t border-slate-200 bg-slate-50">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <x-logo />
                <p class="mt-4 max-w-xs text-sm text-slate-500">Sistem Informasi Survei Kepuasan Pelanggan Perumda Air Minum Tirta Pinang Kota Pangkalpinang.</p>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900">Tautan</p>
                <ul class="mt-4 space-y-2 text-sm text-slate-500">
                    <li><a href="#tentang" class="hover:text-brand-700">Tentang SIMTAG</a></li>
                    <li><a href="#survei" class="hover:text-brand-700">Survei Aktif</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-brand-700">Masuk</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-brand-700">Daftar Akun</a></li>
                </ul>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900">Kontak</p>
                <ul class="mt-4 space-y-3 text-sm text-slate-500">
                    <li class="flex gap-3"><i class="fa-solid fa-location-dot mt-0.5 text-brand-500"></i>Kota Pangkalpinang, Kepulauan Bangka Belitung</li>
                    <li class="flex gap-3"><i class="fa-solid fa-globe mt-0.5 text-brand-500"></i><a href="https://tirtapinang.co.id/" target="_blank" rel="noopener" class="hover:text-brand-700">tirtapinang.co.id</a></li>
                    <li class="flex gap-3"><i class="fa-solid fa-headset mt-0.5 text-brand-500"></i>Pengaduan online melalui menu Pengaduan</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-200 py-5 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Perumda Air Minum Tirta Pinang Kota Pangkalpinang. Seluruh hak cipta dilindungi.
        </div>
    </footer>
</x-layout.base>
