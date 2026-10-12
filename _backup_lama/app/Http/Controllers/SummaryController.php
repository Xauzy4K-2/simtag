<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Complaint;
use App\Models\Survey;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    /**
     * Menampilkan halaman ringkasan (dashboard).
     */
    public function index(Request $request)
    {
        $data = [
            'total_customers'       => Customer::active()->count(),
            'total_bills_unpaid'    => Bill::unpaid()->count(),
            'total_bills_overdue'   => Bill::overdue()->count(),
            'total_unpaid_amount'   => Bill::unpaid()->sum('total_amount'),
            'total_payments_month'  => Payment::success()
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
            'total_complaints_open' => Complaint::open()->count(),
            'total_surveys_active'  => Survey::published()->count(),
            'recent_payments'       => Payment::with(['customer', 'bill'])
                ->success()
                ->latest('paid_at')
                ->limit(5)
                ->get(),
            'recent_complaints'     => Complaint::with('customer')
                ->latest()
                ->limit(5)
                ->get(),
        ];

        // The dashboard is mounted by Vue inside the shared app shell.
        return view('app', $data);
    }
}
