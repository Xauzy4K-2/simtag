<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Daftar semua pelanggan (dengan pencarian & filter status).
     */
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                        ->orWhere('customer_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    /**
     * Form tambah pelanggan baru.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Simpan pelanggan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'email'     => ['required', 'email', 'max:150', 'unique:customers,email'],
            'phone'     => ['required', 'string', 'max:30'],
            'gender'    => ['required', Rule::in(['male', 'female'])],
            'address'   => ['nullable', 'string'],
            'status'    => ['required', Rule::in(['active', 'inactive', 'blocked'])],
        ]);

        $validated['customer_number'] = $this->generateCustomerNumber();

        $customer = Customer::create($validated);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * Detail satu pelanggan beserta riwayat tagihan, pembayaran, dan aduan.
     */
    public function show(Customer $customer)
    {
        $customer->load(['bills' => function ($q) {
            $q->latest()->limit(10);
        }, 'payments' => function ($q) {
            $q->latest('paid_at')->limit(10);
        }, 'complaints' => function ($q) {
            $q->latest()->limit(10);
        }]);

        return view('customers.show', compact('customer'));
    }

    /**
     * Form edit pelanggan.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update data pelanggan.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'email'     => ['required', 'email', 'max:150', Rule::unique('customers', 'email')->ignore($customer->id)],
            'phone'     => ['required', 'string', 'max:30'],
            'gender'    => ['required', Rule::in(['male', 'female'])],
            'address'   => ['nullable', 'string'],
            'status'    => ['required', Rule::in(['active', 'inactive', 'blocked'])],
        ]);

        $customer->update($validated);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    /**
     * Hapus pelanggan.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }

    /**
     * Generate nomor pelanggan unik, format: CUST-000001
     */
    private function generateCustomerNumber(): string
    {
        $last = Customer::orderByDesc('id')->first();
        $nextId = $last ? $last->id + 1 : 1;

        return 'CUST-' . str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);
    }
}
