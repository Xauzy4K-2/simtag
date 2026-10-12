<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtp;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    private const OTP_TTL_MINUTES = 15;

    public function request(): View
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);
        $email = strtolower($data['email']);
        $user = User::where('email', $email)->where('status', 'active')->first();

        if ($user) {
            $otp = (string) random_int(100000, 999999);

            DB::transaction(function () use ($user, $otp) {
                $user->passwordResetTokens()->whereNull('used_at')->update(['used_at' => now()]);
                $user->passwordResetTokens()->create([
                    'otp_hash' => Hash::make($otp),
                    'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
                ]);
            });

            try {
                Mail::to($user->email)->send(new PasswordResetOtp($user, $otp, self::OTP_TTL_MINUTES));
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim OTP reset password', ['email' => $user->email, 'error' => $e->getMessage()]);
            }

            // Mode pengembangan tanpa SMTP: tampilkan OTP agar alur reset bisa diuji.
            if (app()->environment('local') && config('mail.default') === 'log') {
                session()->flash('info', "Mode pengembangan: kode OTP Anda adalah {$otp} (email dicatat di storage/logs/laravel.log).");
            }
        }

        // Pesan dibuat sama agar tidak membocorkan email mana yang terdaftar.
        return redirect()->route('password.reset', ['email' => $email])
            ->with('success', 'Jika email terdaftar, kode OTP 6 digit telah dikirim dan berlaku '.self::OTP_TTL_MINUTES.' menit.');
    }

    public function reset(Request $request): View
    {
        return view('auth.reset-password', ['email' => $request->query('email')]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [], ['otp' => 'kode OTP', 'password' => 'kata sandi']);

        $user = User::where('email', strtolower($data['email']))->first();
        $token = $user?->passwordResetTokens()->valid()->latest('id')->first();

        if (! $token) {
            throw ValidationException::withMessages(['otp' => 'Kode OTP tidak valid atau sudah kedaluwarsa. Silakan minta kode baru.']);
        }

        if (! Hash::check($data['otp'], $token->otp_hash)) {
            $token->increment('attempts');
            $left = PasswordResetToken::MAX_ATTEMPTS - $token->attempts;

            throw ValidationException::withMessages([
                'otp' => $left > 0 ? "Kode OTP salah. Sisa percobaan: {$left}." : 'Terlalu banyak percobaan. Silakan minta kode baru.',
            ]);
        }

        DB::transaction(function () use ($user, $token, $data) {
            $token->update(['used_at' => now()]);
            $user->update(['password_hash' => Hash::make($data['password'])]);
        });

        return redirect()->route('login')->with('success', 'Kata sandi berhasil diperbarui. Silakan masuk dengan kata sandi baru.');
    }
}
