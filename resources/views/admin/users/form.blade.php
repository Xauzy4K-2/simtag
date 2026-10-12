@php $editing = $user->exists; @endphp

<x-layout.admin :title="$editing ? 'Ubah Pengguna' : 'Tambah Pengguna'">
    <x-page-header :title="$editing ? 'Ubah Pengguna' : 'Tambah Pengguna'" :back="route('admin.users.index')" />

    <form method="POST" action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" class="card max-w-3xl" x-data="{ show: false }">
        @csrf
        @if ($editing) @method('PUT') @endif
        <div class="card-body grid gap-5 sm:grid-cols-2">
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
            <div>
                <label for="role" class="label">Peran</label>
                <select id="role" name="role" class="input">
                    <option value="staff" @selected(old('role', $user->role) === 'staff')>Petugas — kelola survei & pengaduan</option>
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Administrator — akses penuh</option>
                </select>
                <x-field-error name="role" />
            </div>
            <div>
                <label for="status" class="label">Status</label>
                <select id="status" name="status" class="input">
                    @foreach (\App\Models\User::STATUSES as $key => $label)
                        <option value="{{ $key }}" @selected(old('status', $user->status) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="password" class="label">Kata Sandi {{ $editing ? 'Baru' : '' }}</label>
                <input id="password" name="password" :type="show ? 'text' : 'password'" type="password" {{ $editing ? '' : 'required' }} autocomplete="new-password" placeholder="{{ $editing ? 'Kosongkan jika tidak diubah' : 'Min. 8 karakter, huruf & angka' }}" class="input @error('password') input-error @enderror">
                <x-field-error name="password" />
            </div>
            <div>
                <label for="password_confirmation" class="label">Konfirmasi Kata Sandi</label>
                <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" type="password" autocomplete="new-password" class="input">
            </div>
            <label class="flex items-center gap-2 text-xs text-slate-500 sm:col-span-2"><input type="checkbox" x-model="show" class="h-4 w-4 accent-brand-600"> Tampilkan kata sandi</label>
        </div>
        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 px-4 py-4 sm:flex-row sm:justify-end sm:px-6">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" data-loading="Menyimpan..."><i class="fa-solid fa-floppy-disk"></i>Simpan</button>
        </div>
    </form>
</x-layout.admin>
