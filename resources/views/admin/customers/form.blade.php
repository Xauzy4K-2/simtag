@php $editing = $customer->exists; @endphp

<x-layout.admin :title="$editing ? 'Ubah Pelanggan' : 'Tambah Pelanggan'">
    <x-page-header :title="$editing ? 'Ubah Data Pelanggan' : 'Tambah Pelanggan Baru'"
                   :subtitle="$editing ? $customer->customer_number : 'Nomor pelanggan dan akun login dibuat otomatis.'"
                   :back="$editing ? route('admin.customers.show', $customer) : route('admin.customers.index')" />

    <form method="POST" action="{{ $editing ? route('admin.customers.update', $customer) : route('admin.customers.store') }}" class="card max-w-4xl">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="card-body grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="full_name" class="label">Nama Lengkap</label>
                <input id="full_name" name="full_name" value="{{ old('full_name', $customer->full_name) }}" required class="input @error('full_name') input-error @enderror">
                <x-field-error name="full_name" />
            </div>
            <div>
                <label for="email" class="label">Email (untuk login)</label>
                <input id="email" name="email" type="email" value="{{ old('email', $customer->email) }}" required class="input @error('email') input-error @enderror">
                <x-field-error name="email" />
            </div>
            <div>
                <label for="phone" class="label">Nomor HP</label>
                <input id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required class="input @error('phone') input-error @enderror">
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
                <select id="district" name="district" class="input @error('district') input-error @enderror">
                    <option value="">Pilih kecamatan</option>
                    @foreach (\App\Models\Customer::DISTRICTS as $district)
                        <option value="{{ $district }}" @selected(old('district', $customer->district) === $district)>{{ $district }}</option>
                    @endforeach
                </select>
                <x-field-error name="district" />
            </div>
            <div class="sm:col-span-2">
                <label for="address" class="label">Alamat</label>
                <textarea id="address" name="address" rows="2" class="input @error('address') input-error @enderror">{{ old('address', $customer->address) }}</textarea>
                <x-field-error name="address" />
            </div>
            <div>
                <label for="status" class="label">Status</label>
                <select id="status" name="status" class="input">
                    @foreach (\App\Models\Customer::STATUSES as $key => $label)
                        <option value="{{ $key }}" @selected(old('status', $customer->status) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="hint">Pelanggan nonaktif/diblokir tidak dapat masuk.</p>
            </div>
            <div>
                <label for="password" class="label">Kata Sandi {{ $editing ? 'Baru' : '' }} <span class="font-normal normal-case text-slate-400">(opsional)</span></label>
                <input id="password" name="password" type="text" autocomplete="new-password" class="input @error('password') input-error @enderror"
                       placeholder="{{ $editing ? 'Kosongkan jika tidak diubah' : 'Kosongkan untuk dibuat otomatis' }}">
                <x-field-error name="password" />
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 px-4 py-4 sm:flex-row sm:justify-end sm:px-6">
            <a href="{{ $editing ? route('admin.customers.show', $customer) : route('admin.customers.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" data-loading="Menyimpan..."><i class="fa-solid fa-floppy-disk"></i>{{ $editing ? 'Simpan Perubahan' : 'Tambah Pelanggan' }}</button>
        </div>
    </form>
</x-layout.admin>
