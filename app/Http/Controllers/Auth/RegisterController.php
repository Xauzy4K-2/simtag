<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email', 'unique:customers,email'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'district' => ['required', Rule::in(Customer::DISTRICTS)],
            'address' => ['required', 'string', 'max:500'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'agree' => ['accepted'],
        ], [
            'agree.accepted' => 'Anda harus menyetujui ketentuan penggunaan data.',
            'phone.regex' => 'Format nomor HP tidak valid.',
        ], [
            'full_name' => 'nama lengkap',
            'phone' => 'nomor HP',
            'gender' => 'jenis kelamin',
            'district' => 'kecamatan',
            'address' => 'alamat',
            'password' => 'kata sandi',
        ]);

        $user = DB::transaction(function () use ($data) {
            $customer = Customer::create([
                'customer_number' => Customer::generateNumber(),
                'full_name' => $data['full_name'],
                'email' => strtolower($data['email']),
                'phone' => $data['phone'],
                'gender' => $data['gender'],
                'district' => $data['district'],
                'address' => $data['address'],
                'status' => 'active',
            ]);

            return User::create([
                'customer_id' => $customer->id,
                'name' => $data['full_name'],
                'email' => strtolower($data['email']),
                'password_hash' => Hash::make($data['password']),
                'role' => 'customer',
                'status' => 'active',
                'last_login_at' => now(),
            ]);
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('app.dashboard')
            ->with('success', 'Pendaftaran berhasil! Nomor pelanggan Anda: '.$user->customer->customer_number);
    }
}
