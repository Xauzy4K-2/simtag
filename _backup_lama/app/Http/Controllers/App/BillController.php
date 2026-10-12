<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('customer');
        abort_unless($user->role === 'customer', 403, 'Halaman ini hanya untuk pelanggan.');
        abort_unless($user->customer, 403, 'Akun ini belum terhubung dengan data pelanggan.');

        $bill = $user->customer->bills()
            ->with(['payments' => fn ($query) => $query->latest('created_at')])
            ->latest('billing_period')
            ->first();

        return view('app', [
            'billPage' => [
                'customer' => [
                    'name' => $user->customer->full_name,
                    'number' => $user->customer->customer_number,
                ],
                'bill' => $bill ? [
                    'period' => $bill->billing_period,
                    'usage_m3' => (float) $bill->water_usage_m3,
                    'water_rate' => (float) $bill->water_rate,
                    'administration_fee' => (float) $bill->administration_fee,
                    'penalty_fee' => (float) $bill->penalty_fee,
                    'total_amount' => (float) $bill->total_amount,
                    'status' => $bill->status,
                    'due_date' => $bill->due_date?->format('Y-m-d'),
                    'payment' => $bill->payments->first() ? [
                        'method' => $bill->payments->first()->payment_method,
                        'reference' => $bill->payments->first()->payment_reference,
                        'status' => $bill->payments->first()->status,
                    ] : null,
                ] : null,
            ],
        ]);
    }
}
