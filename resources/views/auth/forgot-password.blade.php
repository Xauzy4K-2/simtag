<x-layout.guest title="Lupa Kata Sandi" heading="Lupa kata sandi?" subheading="Masukkan email terdaftar. Kami akan mengirim kode OTP 6 digit untuk membuat kata sandi baru.">
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="label">Email Terdaftar</label>
            <div class="relative">
                <i class="fa-solid fa-envelope pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com" class="input pl-10 @error('email') input-error @enderror">
            </div>
            <x-field-error name="email" />
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-full" data-loading="Mengirim kode...">
            <i class="fa-solid fa-paper-plane"></i>Kirim Kode OTP
        </button>
    </form>

    <div class="mt-6 flex flex-col items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('password.reset') }}" class="font-semibold text-brand-600 hover:underline">Saya sudah punya kode OTP</a>
        <a href="{{ route('login') }}" class="hover:text-slate-700"><i class="fa-solid fa-arrow-left mr-1 text-xs"></i>Kembali ke halaman masuk</a>
    </div>
</x-layout.guest>
