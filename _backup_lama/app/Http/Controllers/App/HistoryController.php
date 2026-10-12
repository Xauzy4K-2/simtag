<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('customer');
        abort_unless($user->role === 'customer', 403, 'Halaman ini hanya untuk pelanggan.');
        abort_unless($user->customer, 403, 'Akun ini belum terhubung dengan data pelanggan.');

        $customer = $user->customer;
        $activities = collect()
            ->merge($customer->payments()->latest('created_at')->get()->map(fn ($payment) => [
                'type' => 'Pembayaran',
                'title' => 'Pembayaran tagihan',
                'description' => $payment->payment_reference,
                'status' => $payment->status,
                'date' => $payment->created_at,
                'amount' => (float) $payment->amount,
            ]))
            ->merge($customer->complaints()->latest()->get()->map(fn ($complaint) => [
                'type' => 'Pengaduan',
                'title' => $complaint->subject,
                'description' => $complaint->description,
                'status' => $complaint->status,
                'date' => $complaint->created_at,
                'amount' => null,
            ]))
            ->merge($customer->bills()->latest()->get()->map(fn ($bill) => [
                'type' => 'Tagihan',
                'title' => 'Tagihan periode '.$bill->billing_period,
                'description' => 'Pemakaian '.$bill->water_usage_m3.' m³',
                'status' => $bill->status,
                'date' => $bill->created_at,
                'amount' => (float) $bill->total_amount,
            ]))
            ->sortByDesc('date')
            ->values()
            ->map(fn (array $activity) => [
                ...$activity,
                'date' => $activity['date']?->format('Y-m-d H:i'),
            ]);

        return view('app', ['historyPage' => ['activities' => $activities]]);
    }
}
