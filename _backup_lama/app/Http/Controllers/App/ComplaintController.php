<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    private function customer(Request $request)
    {
        $user = $request->user()->load('customer');
        abort_unless($user->role === 'customer', 403, 'Halaman ini hanya untuk pelanggan.');
        abort_unless($user->customer, 403, 'Akun ini belum terhubung dengan data pelanggan.');

        return $user->customer;
    }

    public function index(Request $request)
    {
        $customer = $this->customer($request);
        $latest = $customer->complaints()->latest()->first();

        return view('app', [
            'complaintPage' => [
                'customer' => [
                    'name' => $customer->full_name,
                    'number' => $customer->customer_number,
                ],
                'latest' => $latest ? [
                    'subject' => $latest->subject,
                    'description' => $latest->description,
                    'status' => $latest->status,
                    'created_at' => $latest->created_at?->format('d/m/Y H:i'),
                    'attachment_url' => $latest->attachment_url
                        ? '/storage/' . ltrim($latest->attachment_url, '/')
                        : null,
                ] : null,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $customer = $this->customer($request);
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        if ($request->hasFile('attachment')) {
            $validated['attachment_url'] = $request->file('attachment')->store('complaints', 'public');
        }
        unset($validated['attachment']);
        $validated['customer_id'] = $customer->id;
        $validated['status'] = 'open';
        Complaint::create($validated);

        return redirect()->route('app.complaints')->with('success', 'Pengaduan berhasil dikirim.');
    }
}
