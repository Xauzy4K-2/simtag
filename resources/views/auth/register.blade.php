<x-layout.guest title="Daftar Akun" heading="Buat akun pelanggan" subheading="Daftar gratis untuk mengisi survei kepuasan dan mengirim pengaduan.">
    <form method="POST" action="{{ route('register.store') }}" class="space-y-5" x-data="{ show: false }">
        @csrf

        <div>
            <label for="full_name" class="label">Nama Lengkap</label>
            <input id="full_name" name="full_name" value="{{ old('full_name') }}" required autofocus autocomplete="name" placeholder="Sesuai KTP" class="input @error('full_name') input-error @enderror">
            <x-field-error name="full_name" />
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="email" class="label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nama@email.com" class="input @error('email') input-error @enderror">
                <x-field-error name="email" />
            </div>
            <div>
                <label for="phone" class="label">Nomor HP / WhatsApp</label>
                <input id="phone" name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" required autocomplete="tel" placeholder="08xxxxxxxxxx" class="input @error('phone') input-error @enderror">
                <x-field-error name="phone" />
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <span class="label">Jenis Kelamin</span>
                <div class="grid grid-cols-2 gap-2">
                    @foreach (['male' => 'Laki-laki', 'female' => 'Perempuan'] as $value => $label)
                        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-600 transition has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                            <input type="radio" name="gender" value="{{ $value }}" class="sr-only" @checked(old('gender', 'male') === $value)>{{ $label }}
                        </label>
                    @endforeach
                </div>
                <x-field-error name="gender" />
            </div>
            <div>
                <label for="district" class="label">Kecamatan</label>
                <select id="district" name="district" required class="input @error('district') input-error @enderror">
                    <option value="">Pilih kecamatan</option>
                    @foreach (\App\Models\Customer::DISTRICTS as $district)
                        <option value="{{ $district }}" @selected(old('district') === $district)>{{ $district }}</option>
                    @endforeach
                </select>
                <x-field-error name="district" />
            </div>
        </div>

        <div>
            <label for="address" class="label">Alamat Lengkap</label>
            <textarea id="address" name="address" rows="2" required placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan" class="input @error('address') input-error @enderror">{{ old('address') }}</textarea>
            <x-field-error name="address" />
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="password" class="label">Kata Sandi</label>
                <input id="password" name="password" :type="show ? 'text' : 'password'" type="password" required autocomplete="new-password" placeholder="Min. 8 karakter" class="input @error('password') input-error @enderror">
                <x-field-error name="password" />
            </div>
            <div>
                <label for="password_confirmation" class="label">Ulangi Kata Sandi</label>
                <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" type="password" required autocomplete="new-password" placeholder="Ketik ulang" class="input">
            </div>
        </div>
        <label class="-mt-2 flex items-center gap-2 text-xs text-slate-500">
            <input type="checkbox" x-model="show" class="h-4 w-4 rounded accent-brand-600"> Tampilkan kata sandi
        </label>
        <p class="hint -mt-3">Gunakan kombinasi huruf dan angka.</p>

        <div>
            <label class="flex items-start gap-2.5 text-sm text-slate-600">
                <input type="checkbox" name="agree" value="1" class="mt-0.5 h-4 w-4 rounded accent-brand-600" @checked(old('agree'))>
                <span>Saya menyetujui data saya digunakan oleh PDAM Tirta Pinang untuk keperluan survei dan pelayanan pelanggan.</span>
            </label>
            <x-field-error name="agree" />
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-full" data-loading="Mendaftarkan...">
            <i class="fa-solid fa-user-plus"></i>Daftar Sekarang
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500">
        Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:underline">Masuk</a>
    </p>
</x-layout.guest>
