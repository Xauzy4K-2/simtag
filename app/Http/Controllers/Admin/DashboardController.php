<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Services\SurveyStatistics;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(SurveyStatistics $statistics): View
    {
        $overall = $statistics->overall();
        $thisMonth = SurveyResponse::where('submitted_at', '>=', now()->startOfMonth())->count();
        $lastMonth = SurveyResponse::whereBetween('submitted_at', [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()])->count();

        return view('admin.dashboard', [
            'stats' => [
                'customers' => Customer::count(),
                'activeCustomers' => Customer::active()->count(),
                'activeSurveys' => Survey::openForResponses()->count(),
                'totalSurveys' => Survey::count(),
                'responses' => $overall['total'],
                'responsesThisMonth' => $thisMonth,
                'responsesGrowth' => $lastMonth > 0 ? round(($thisMonth - $lastMonth) / $lastMonth * 100) : null,
                'openComplaints' => Complaint::active()->count(),
                'newComplaints' => Complaint::where('status', 'open')->count(),
            ],
            'overall' => $overall,
            'trend' => $statistics->monthlyTrend(6),
            'districts' => $statistics->byDistrict(),
            'weakest' => $statistics->weakestAspects(5),
            'surveys' => Survey::openForResponses()->withCount('responses')->latest('published_at')->take(5)->get(),
            'recentResponses' => SurveyResponse::with(['customer', 'survey'])->latest('submitted_at')->take(6)->get(),
            'recentComplaints' => Complaint::with('customer')->latest()->take(6)->get(),
            'complaintStatus' => collect(Complaint::STATUSES)->map(fn ($label, $key) => [
                'label' => $label,
                'total' => Complaint::where('status', $key)->count(),
            ]),
        ]);
    }
}
