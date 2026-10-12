<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@tirtapinang.co.id'], [
            'name' => 'Administrator SIMTAG',
            'password_hash' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        User::updateOrCreate(['email' => 'petugas@tirtapinang.co.id'], [
            'name' => 'Rina Petugas Layanan',
            'password_hash' => Hash::make('petugas123'),
            'role' => 'staff',
            'status' => 'active',
        ]);

        // Akun pelanggan demo
        $customer = Customer::updateOrCreate(['email' => 'budi.pelanggan@example.com'], [
            'customer_number' => 'TP-26100001',
            'full_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'gender' => 'male',
            'district' => 'Gerunggang',
            'address' => 'Jl. Melati No. 12, Kel. Kacang Pedang, Pangkalpinang',
            'status' => 'active',
        ]);

        User::updateOrCreate(['email' => 'budi.pelanggan@example.com'], [
            'customer_id' => $customer->id,
            'name' => $customer->full_name,
            'password_hash' => Hash::make('pelanggan123'),
            'role' => 'customer',
            'status' => 'active',
        ]);
    }
}
