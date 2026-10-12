<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string'],
        ], [], ['login' => 'email / nomor pelanggan', 'password' => 'kata sandi']);

        // Pelanggan boleh masuk memakai nomor pelanggan.
        $email = $credentials['login'];
        if (! str_contains($email, '@')) {
            $customer = Customer::where('customer_number', strtoupper(trim($email)))->first();
            $email = $customer?->user?->email ?? $email;
        }

        if (! Auth::attempt(['email' => $email, 'password' => $credentials['password']], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'login' => 'Email/nomor pelanggan atau kata sandi salah.',
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user->status !== 'active' || ($user->isCustomer() && $user->customer?->status !== 'active')) {
            Auth::logout();

            throw ValidationException::withMessages([
                'login' => 'Akun Anda sedang tidak aktif. Silakan hubungi PDAM Tirta Pinang.',
            ]);
        }

        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->save();

        if ($user->isInternal()) {
            AdminActivityLog::record('Masuk ke sistem', $user);
        }

        return redirect()->intended($user->homeRoute())
            ->with('success', 'Selamat datang kembali, '.$user->name.'!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil keluar.');
    }
}
