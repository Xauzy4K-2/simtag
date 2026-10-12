<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    private function user(Request $request)
    {
        $user = $request->user()->load('customer');
        abort_unless($user->role === 'customer', 403, 'Halaman ini hanya untuk pelanggan.');
        abort_unless($user->customer, 403, 'Akun ini belum terhubung dengan data pelanggan.');

        return $user;
    }

    public function index(Request $request)
    {
        $user = $this->user($request);

        return view('app', [
            'profilePage' => [
                'name' => $user->customer->full_name,
                'email' => $user->email,
                'address' => $user->customer->address,
                'phone' => $user->customer->phone,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = $this->user($request);
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'address' => ['nullable', 'string'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $user->customer->update([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
        ]);
        $user->update(['email' => $validated['email']]);

        return redirect()->route('app.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = $this->user($request);
        $validated = $request->validate([
            'current_password' => ['required', 'current_password:web'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update(['password_hash' => Hash::make($validated['password'])]);

        return redirect()->route('app.profile')->with('success', 'Password berhasil diperbarui.');
    }
}
