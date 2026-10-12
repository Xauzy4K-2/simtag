<x-layout.guest title="Atur Ulang Kata Sandi" heading="Atur ulang kata sandi" subheading="Masukkan kode OTP yang dikirim ke email Anda beserta kata sandi baru.">
    <form method="POST" action="{{ route('password.update') }}" class="space-y-5" x-data="{ show: false }">
        @csrf
        <div>
            <label for="email" class="label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required class="input @error('email') input-error @enderror">
            <x-field-error name="email" />
        </div>

        <div>
            <label for="otp" class="label">Kode OTP</label>
            <input id="otp" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" value="{{ old('otp') }}" required autocomplete="one-time-code"
                   placeholder="••••••" class="input text-center font-mono text-2xl tracking-[0.6em] @error('otp') input-error @enderror" {{ $email ? 'autofocus' : '' }}>
            <x-field-error name="otp" />
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="password" class="label">Kata Sandi Baru</label>
                <input id="password" name="password" :type="show ? 'text' : 'password'" type="password" required autocomplete="new-password" class="input @error('password') input-error @enderror">
                <x-field-error name="password" />
            </div>
            <div>
                <label for="password_confirmation" class="label">Ulangi Kata Sandi</label>
                <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" type="password" required autocomplete="new-password" class="input">
            </div>
        </div>
        <label class="-mt-2 flex items-center gap-2 text-xs text-slate-500">
            <input type="checkbox" x-model="show" class="h-4 w-4 rounded accent-brand-600"> Tampilkan kata sandi
        </label>

        <button type="submit" class="btn btn-primary btn-lg w-full" data-loading="Menyimpan...">
            <i class="fa-solid fa-key"></i>Simpan Kata Sandi Baru
        </button>
    </form>

    <div class="mt-6 flex flex-col items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('password.request') }}" class="font-semibold text-brand-600 hover:underline">Kirim ulang kode OTP</a>
        <a href="{{ route('login') }}" class="hover:text-slate-700"><i class="fa-solid fa-arrow-left mr-1 text-xs"></i>Kembali ke halaman masuk</a>
    </div>
</x-layout.guest>
