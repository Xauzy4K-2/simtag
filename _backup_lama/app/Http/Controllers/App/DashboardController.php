<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Complaint;
use App\Models\Survey;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('customer');
        abort_unless($user->role === 'customer', 403, 'Halaman ini hanya untuk pelanggan.');
        $customer = $user->customer;

        abort_unless($customer, 403, 'Akun ini belum terhubung dengan data pelanggan.');

        $currentPeriod = now()->format('Y-m');
        $bills = $customer->bills()
            ->whereIn('billing_period', collect(range(2, 0))->map(
                fn (int $monthsAgo) => now()->subMonths($monthsAgo)->format('Y-m')
            ))
            ->get()
            ->keyBy('billing_period');

        $usage = collect(range(2, 0))->map(function (int $monthsAgo) use ($bills) {
            $date = now()->subMonths($monthsAgo);
            $period = $date->format('Y-m');
            $bill = $bills->get($period);

            return [
                'period' => $period,
                'month' => $date->locale('id')->monthName,
                'usage_m3' => (float) ($bill?->water_usage_m3 ?? 0),
            ];
        })->values();

        $currentBill = $bills->get($currentPeriod)
            ?? $customer->bills()->where('billing_period', $currentPeriod)->first();

        $openComplaint = $customer->complaints()
            ->whereIn('status', ['open', 'in_progress'])
            ->latest()
            ->first();

        $answeredSurveyIds = $customer->surveyAnswers()
            ->pluck('survey_id')
            ->unique()
            ->values();

        return view('app', [
            'dashboard' => [
                'customer' => [
                    'name' => $customer->full_name,
                    'number' => $customer->customer_number,
                    'status' => $customer->status,
                    'email' => $customer->email,
                ],
                'bill' => $currentBill ? [
                    'amount' => (float) $currentBill->total_amount,
                    'status' => $currentBill->status,
                    'due_date' => $currentBill->due_date?->format('Y-m-d'),
                    'period' => $currentBill->billing_period,
                ] : null,
                'usage' => [
                    'items' => $usage,
                    'average' => round($usage->avg('usage_m3'), 2),
                ],
                'quality' => [
                    'clarity' => null,
                    'pressure' => null,
                    'ph' => null,
                ],
                'open_complaint' => $openComplaint ? [
                    'subject' => $openComplaint->subject,
                    'status' => $openComplaint->status,
                ] : null,
                'published_surveys' => Survey::published()
                    ->whereNotIn('id', $answeredSurveyIds)
                    ->count(),
            ],
        ]);
    }
}
