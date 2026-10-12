<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(Request $request): View
    {
        $customer = $request->user()->customer;
        $type = $request->query('type');

        $surveys = $type === 'complaint' ? collect() : $customer->surveyResponses()->with('survey')->get()
            ->map(fn ($response) => [
                'type' => 'survey',
                'title' => $response->survey?->title ?? 'Survei',
                'description' => 'Skor rata-rata: '.($response->average_score ? number_format($response->average_score, 1).' / 5' : '-'),
                'badge' => $response->average_score ? \App\Models\SurveyResponse::satisfactionLabel($response->average_score) : 'Terkirim',
                'status' => 'resolved',
                'date' => $response->submitted_at,
                'url' => $response->survey ? route('app.surveys.show', $response->survey) : null,
            ]);

        $complaints = $type === 'survey' ? collect() : $customer->complaints()->get()
            ->map(fn ($complaint) => [
                'type' => 'complaint',
                'title' => $complaint->subject,
                'description' => $complaint->ticket_number.' • '.$complaint->category,
                'badge' => $complaint->status_label,
                'status' => $complaint->status,
                'date' => $complaint->created_at,
                'url' => route('app.complaints.show', $complaint),
            ]);

        $activities = $surveys->concat($complaints)->sortByDesc('date')->values();

        return view('customer.history', [
            'groups' => $activities->groupBy(fn ($a) => $a['date']->translatedFormat('F Y')),
            'total' => $activities->count(),
            'type' => $type,
        ]);
    }
}
