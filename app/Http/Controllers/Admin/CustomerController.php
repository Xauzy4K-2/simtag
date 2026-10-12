<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->withCount(['surveyResponses', 'complaints'])
            ->when($request->search, function ($query, $search) {
                $query->where(fn ($q) => $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('customer_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%"));
            })
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->district, fn ($q, $district) => $q->where('district', $district))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('admin.customers.form', ['customer' => new Customer(['status' => 'active', 'gender' => 'male'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $password = $data['password'] ?: Str::password(10, symbols: false);

        $customer = DB::transaction(function () use ($data, $password) {
            $customer = Customer::create([
                ...collect($data)->except('password')->all(),
                'customer_number' => Customer::generateNumber(),
                'email' => strtolower($data['email']),
            ]);

            User::create([
                'customer_id' => $customer->id,
                'name' => $customer->full_name,
                'email' => $customer->email,
                'password_hash' => Hash::make($password),
                'role' => 'customer',
                'status' => 'active',
            ]);

            return $customer;
        });

        AdminActivityLog::record('Menambahkan pelanggan '.$customer->full_name, $customer);

        return redirect()->route('admin.customers.show', $customer)->with([
            'success' => 'Pelanggan berhasil ditambahkan.',
            'credential' => ['email' => $customer->email, 'password' => $password],
        ]);
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'user',
            'surveyResponses' => fn ($q) => $q->with('survey')->latest('submitted_at'),
            'complaints' => fn ($q) => $q->latest(),
        ]);

        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $this->validated($request, $customer);

        DB::transaction(function () use ($customer, $data) {
            $customer->update([...collect($data)->except('password')->all(), 'email' => strtolower($data['email'])]);

            $customer->user?->update(array_filter([
                'name' => $customer->full_name,
                'email' => $customer->email,
                'password_hash' => $data['password'] ? Hash::make($data['password']) : null,
            ]));
        });

        AdminActivityLog::record('Memperbarui data pelanggan '.$customer->full_name, $customer);

        return redirect()->route('admin.customers.show', $customer)->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        AdminActivityLog::record('Menghapus pelanggan '.$customer->full_name.' ('.$customer->customer_number.')', $customer);
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Pelanggan beserta data survei & pengaduannya berhasil dihapus.');
    }

    public function resetPassword(Customer $customer): RedirectResponse
    {
        abort_unless($customer->user, 404);
        $password = Str::password(10, symbols: false);
        $customer->user->update(['password_hash' => Hash::make($password)]);

        AdminActivityLog::record('Mereset kata sandi pelanggan '.$customer->full_name, $customer);

        return back()->with([
            'success' => 'Kata sandi pelanggan berhasil direset.',
            'credential' => ['email' => $customer->user->email, 'password' => $password],
        ]);
    }

    private function validated(Request $request, ?Customer $customer = null): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('customers', 'email')->ignore($customer?->id),
                Rule::unique('users', 'email')->ignore($customer?->user?->id),
            ],
            'phone' => ['required', 'string', 'max:30'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'district' => ['nullable', Rule::in(Customer::DISTRICTS)],
            'address' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(array_keys(Customer::STATUSES))],
            'password' => ['nullable', 'string', 'min:8'],
        ], [], [
            'full_name' => 'nama lengkap',
            'phone' => 'nomor HP',
            'district' => 'kecamatan',
            'address' => 'alamat',
            'password' => 'kata sandi',
        ]);
    }
}
