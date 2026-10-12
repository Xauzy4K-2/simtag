<x-layout.customer title="Profil">
    <x-page-header title="Pengaturan Akun" subtitle="Kelola data diri dan keamanan akun Anda." />

    <div class="grid gap-6 lg:grid-cols-3">
        <aside class="card card-body h-fit text-center">
            <span class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-sky-500 text-3xl font-extrabold text-white ring-4 ring-brand-50">{{ $customer->initials }}</span>
            <p class="mt-4 text-lg font-bold text-slate-900">{{ $customer->full_name }}</p>
            <p class="font-mono text-sm text-slate-500">{{ $customer->customer_number }}</p>
            <div class="mt-3"><x-status-badge :status="$customer->status" /></div>
            <dl class="mt-6 space-y-3 border-t border-slate-100 pt-5 text-left text-sm">
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Terdaftar</dt><dd class="font-semibold text-slate-700">{{ $customer->created_at->translatedFormat('d M Y') }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Survei diisi</dt><dd class="font-semibold text-slate-700">{{ $customer->surveyResponses()->count() }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Login terakhir</dt><dd class="font-semibold text-slate-700">{{ $user->last_login_at?->diffForHumans() ?? '-' }}</dd></div>
            </dl>
        </aside>

        <div class="space-y-6 lg:col-span-2">
            <form method="POST" action="{{ route('app.profile.update') }}" class="card">
                @csrf @method('PUT')
                <div class="card-header"><h2 class="card-title"><i class="fa-solid fa-id-card mr-1.5 text-brand-500"></i>Data Diri</h2></div>
                <div class="card-body grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="full_name" class="label">Nama Lengkap</label>
                        <input id="full_name" name="full_name" value="{{ old('full_name', $customer->full_name) }}" required class="input @error('full_name') input-error @enderror">
                        <x-field-error name="full_name" />
                    </div>
                    <div>
                        <label for="email" class="label">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $customer->email) }}" required class="input @error('email') input-error @enderror">
                        <x-field-error name="email" />
                    </div>
                    <div>
                        <label for="phone" class="label">Nomor HP</label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone', $customer->phone) }}" required class="input @error('phone') input-error @enderror">
                        <x-field-error name="phone" />
                    </div>
                    <div>
                        <label for="gender" class="label">Jenis Kelamin</label>
                        <select id="gender" name="gender" class="input">
                            <option value="male" @selected(old('gender', $customer->gender) === 'male')>Laki-laki</option>
                            <option value="female" @selected(old('gender', $customer->gender) === 'female')>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label for="district" class="label">Kecamatan</label>
                        <select id="district" name="district" required class="input @error('district') input-error @enderror">
                            <option value="">Pilih kecamatan</option>
                            @foreach (\App\Models\Customer::DISTRICTS as $district)
                                <option value="{{ $district }}" @selected(old('district', $customer->district) === $district)>{{ $district }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="district" />
                    </div>
                    <div class="sm:col-span-2">
                        <label for="address" class="label">Alamat</label>
                        <textarea id="address" name="address" rows="2" required class="input @error('address') input-error @enderror">{{ old('address', $customer->address) }}</textarea>
                        <x-field-error name="address" />
                    </div>
                    <div class="sm:col-span-2 flex justify-end">
                        <button type="submit" class="btn btn-primary w-full sm:w-auto" data-loading="Menyimpan..."><i class="fa-solid fa-floppy-disk"></i>Simpan Perubahan</button>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('app.profile.password') }}" class="card" x-data="{ show: false }">
                @csrf @method('PUT')
                <div class="card-header"><h2 class="card-title"><i class="fa-solid fa-shield-halved mr-1.5 text-sky-500"></i>Ubah Kata Sandi</h2></div>
                <div class="card-body grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="current_password" class="label">Kata Sandi Saat Ini</label>
                        <input id="current_password" name="current_password" :type="show ? 'text' : 'password'" type="password" required autocomplete="current-password" class="input @error('current_password', 'password') input-error @enderror">
                        <x-field-error name="current_password" bag="password" />
                    </div>
                    <div>
                        <label for="password" class="label">Kata Sandi Baru</label>
                        <input id="password" name="password" :type="show ? 'text' : 'password'" type="password" required autocomplete="new-password" class="input @error('password', 'password') input-error @enderror">
                        <x-field-error name="password" bag="password" />
                    </div>
                    <div>
                        <label for="password_confirmation" class="label">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" type="password" required autocomplete="new-password" class="input">
                    </div>
                    <div class="flex flex-col gap-3 sm:col-span-2 sm:flex-row sm:items-center sm:justify-between">
                        <label class="flex items-center gap-2 text-xs text-slate-500"><input type="checkbox" x-model="show" class="h-4 w-4 accent-sky-600"> Tampilkan kata sandi</label>
                        <button type="submit" class="btn btn-sky w-full sm:w-auto" data-loading="Menyimpan..."><i class="fa-solid fa-key"></i>Perbarui Kata Sandi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layout.customer>
