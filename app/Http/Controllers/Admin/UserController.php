<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::internal()
            ->when($request->search, fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")))
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User(['role' => 'staff', 'status' => 'active'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'role' => $data['role'],
            'status' => $data['status'],
            'password_hash' => Hash::make($data['password']),
        ]);

        AdminActivityLog::record('Menambahkan pengguna '.$user->name.' ('.$user->role_label.')', $user);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        abort_if($user->isCustomer(), 404);

        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isCustomer(), 404);
        $data = $this->validated($request, $user);

        if ($user->is($request->user()) && ($data['role'] !== 'admin' || $data['status'] !== 'active')) {
            return back()->with('error', 'Anda tidak dapat menurunkan role atau menonaktifkan akun Anda sendiri.');
        }

        $user->update(array_filter([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'role' => $data['role'],
            'status' => $data['status'],
            'password_hash' => $data['password'] ? Hash::make($data['password']) : null,
        ]));

        AdminActivityLog::record('Memperbarui pengguna '.$user->name, $user);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isCustomer(), 404);

        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Minimal harus ada satu administrator.');
        }

        AdminActivityLog::record('Menghapus pengguna '.$user->name, $user);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user?->id)],
            'role' => ['required', Rule::in(['admin', 'staff'])],
            'status' => ['required', Rule::in(array_keys(User::STATUSES))],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [], ['name' => 'nama', 'password' => 'kata sandi']);
    }
}
