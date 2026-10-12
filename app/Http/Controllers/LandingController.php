<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Survey;
use App\Services\SurveyStatistics;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(SurveyStatistics $statistics): View
    {
        $overall = $statistics->overall();

        return view('landing', [
            'stats' => [
                'customers' => Customer::active()->count(),
                'responses' => $overall['total'],
                'index' => $overall['index'],
                'label' => $overall['label'],
                'resolved' => Complaint::whereIn('status', ['resolved', 'closed'])->count(),
            ],
            'activeSurveys' => Survey::openForResponses()->withCount('questions')->latest('published_at')->take(3)->get(),
        ]);
    }
}
