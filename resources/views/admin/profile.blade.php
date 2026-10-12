<x-layout.admin title="Profil Saya">
    <x-page-header title="Profil Saya" subtitle="Perbarui informasi akun dan kata sandi Anda." />

    <div class="grid max-w-5xl gap-6 lg:grid-cols-2">
        <form method="POST" action="{{ route('admin.profile.update') }}" class="card">
            @csrf @method('PUT')
            <div class="card-header"><h2 class="card-title"><i class="fa-solid fa-id-badge mr-1.5 text-brand-500"></i>Informasi Akun</h2></div>
            <div class="card-body space-y-5">
                <div class="flex items-center gap-4">
                    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-900 text-lg font-bold text-brand-300">{{ $user->initials }}</span>
                    <div><p class="font-bold text-slate-800">{{ $user->role_label }}</p><p class="text-xs text-slate-500">Bergabung {{ $user->created_at->translatedFormat('d M Y') }}</p></div>
                </div>
                <div>
                    <label for="name" class="label">Nama</label>
                    <input id="name" name="name" value="{{ old('name', $user->name) }}" required class="input @error('name') input-error @enderror">
                    <x-field-error name="name" />
                </div>
                <div>
                    <label for="email" class="label">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="input @error('email') input-error @enderror">
                    <x-field-error name="email" />
                </div>
                <div class="flex justify-end"><button type="submit" class="btn btn-primary" data-loading="Menyimpan..."><i class="fa-solid fa-floppy-disk"></i>Simpan</button></div>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.profile.password') }}" class="card" x-data="{ show: false }">
            @csrf @method('PUT')
            <div class="card-header"><h2 class="card-title"><i class="fa-solid fa-shield-halved mr-1.5 text-sky-500"></i>Ubah Kata Sandi</h2></div>
            <div class="card-body space-y-5">
                <div>
                    <label for="current_password" class="label">Kata Sandi Saat Ini</label>
                    <input id="current_password" name="current_password" :type="show ? 'text' : 'password'" type="password" required class="input @error('current_password', 'password') input-error @enderror">
                    <x-field-error name="current_password" bag="password" />
                </div>
                <div>
                    <label for="password" class="label">Kata Sandi Baru</label>
                    <input id="password" name="password" :type="show ? 'text' : 'password'" type="password" required autocomplete="new-password" class="input @error('password', 'password') input-error @enderror">
                    <x-field-error name="password" bag="password" />
                </div>
                <div>
                    <label for="password_confirmation" class="label">Konfirmasi Kata Sandi Baru</label>
                    <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" type="password" required autocomplete="new-password" class="input">
                </div>
                <div class="flex items-center justify-between gap-3">
                    <label class="flex items-center gap-2 text-xs text-slate-500"><input type="checkbox" x-model="show" class="h-4 w-4 accent-sky-600"> Tampilkan</label>
                    <button type="submit" class="btn btn-sky" data-loading="Menyimpan..."><i class="fa-solid fa-key"></i>Perbarui</button>
                </div>
            </div>
        </form>
    </div>
</x-layout.admin>
