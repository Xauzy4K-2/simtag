<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('customer.profile', [
            'user' => $request->user(),
            'customer' => $request->user()->customer,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $customer = $user->customer;

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
                Rule::unique('customers', 'email')->ignore($customer->id),
            ],
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'district' => ['required', Rule::in(Customer::DISTRICTS)],
            'address' => ['required', 'string', 'max:500'],
        ], ['phone.regex' => 'Format nomor HP tidak valid.'], [
            'full_name' => 'nama lengkap',
            'phone' => 'nomor HP',
            'district' => 'kecamatan',
            'address' => 'alamat',
        ]);

        DB::transaction(function () use ($user, $customer, $data) {
            $customer->update([...$data, 'email' => strtolower($data['email'])]);
            $user->update(['name' => $data['full_name'], 'email' => strtolower($data['email'])]);
        });

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function password(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::min(8)->letters()->numbers()],
        ], [
            'current_password.current_password' => 'Kata sandi saat ini salah.',
        ], [
            'current_password' => 'kata sandi saat ini',
            'password' => 'kata sandi baru',
        ]);

        $request->user()->update(['password_hash' => Hash::make($data['password'])]);

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
