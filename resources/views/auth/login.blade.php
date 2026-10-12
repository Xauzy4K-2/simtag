<x-layout.guest title="Masuk" heading="Selamat datang kembali 👋" subheading="Masuk untuk mengisi survei dan memantau pengaduan Anda.">
    <form method="POST" action="{{ route('login.store') }}" class="space-y-5" x-data="{ show: false }">
        @csrf
        <div>
            <label for="login" class="label">Email / Nomor Pelanggan</label>
            <div class="relative">
                <i class="fa-solid fa-envelope pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus autocomplete="username"
                       placeholder="nama@email.com atau TP-xxxx" class="input pl-10 @error('login') input-error @enderror">
            </div>
            <x-field-error name="login" />
        </div>

        <div>
            <div class="mb-1.5 flex items-center justify-between">
                <label for="password" class="label mb-0">Kata Sandi</label>
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-brand-600 hover:underline">Lupa kata sandi?</a>
            </div>
            <div class="relative">
                <i class="fa-solid fa-lock pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input id="password" name="password" :type="show ? 'text' : 'password'" type="password" required autocomplete="current-password"
                       placeholder="Masukkan kata sandi" class="input px-10 @error('password') input-error @enderror">
                <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-600" aria-label="Tampilkan kata sandi">
                    <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            <x-field-error name="password" />
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 accent-brand-600" @checked(old('remember'))>
            Ingat saya di perangkat ini
        </label>

        <button type="submit" class="btn btn-primary btn-lg w-full" data-loading="Memeriksa...">
            <i class="fa-solid fa-right-to-bracket"></i>Masuk
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500">
        Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-brand-600 hover:underline">Daftar di sini</a>
    </p>

    @if (app()->environment('local'))
        <div class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 text-xs text-slate-500">
            <p class="mb-2 font-bold text-slate-600"><i class="fa-solid fa-flask mr-1"></i>Akun demo (mode pengembangan)</p>
            <ul class="space-y-1 font-mono">
                <li>Admin: admin@tirtapinang.co.id / admin123</li>
                <li>Petugas: petugas@tirtapinang.co.id / petugas123</li>
                <li>Pelanggan: budi.pelanggan@example.com / pelanggan123</li>
            </ul>
        </div>
    @endif
</x-layout.guest>
