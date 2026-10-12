<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $customer = $request->user()->customer;
        $answeredIds = $customer->surveyResponses()->pluck('survey_id');

        $pendingSurveys = Survey::openForResponses()
            ->whereNotIn('id', $answeredIds)
            ->withCount('questions')
            ->latest('published_at')
            ->get();

        return view('customer.dashboard', [
            'customer' => $customer,
            'pendingSurveys' => $pendingSurveys,
            'stats' => [
                'pending' => $pendingSurveys->count(),
                'completed' => $answeredIds->count(),
                'activeComplaints' => $customer->complaints()->active()->count(),
                'totalComplaints' => $customer->complaints()->count(),
            ],
            'recentResponses' => $customer->surveyResponses()->with('survey')->latest('submitted_at')->take(3)->get(),
            'recentComplaints' => $customer->complaints()->latest()->take(3)->get(),
        ]);
    }
}
