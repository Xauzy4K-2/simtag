<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Services\SurveyStatistics;
use App\Support\XlsxWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SurveyResultController extends Controller
{
    public function show(Request $request, Survey $survey, SurveyStatistics $statistics): View
    {
        $responses = $survey->responses()
            ->with('customer')
            ->when($request->search, fn ($q, $s) => $q->whereHas('customer', fn ($c) => $c
                ->where('full_name', 'like', "%{$s}%")
                ->orWhere('customer_number', 'like', "%{$s}%")))
            ->latest('submitted_at')
            ->paginate(15, ['*'], 'halaman')
            ->withQueryString();

        return view('admin.surveys.results', [
            'survey' => $survey,
            'stats' => $statistics->forSurvey($survey),
            'responses' => $responses,
            'suggestions' => $survey->responses()->with('customer')->whereNotNull('suggestion')->where('suggestion', '!=', '')->latest('submitted_at')->take(10)->get(),
        ]);
    }

    public function print(Survey $survey, SurveyStatistics $statistics): View
    {
        return view('admin.surveys.print', [
            'survey' => $survey,
            'stats' => $statistics->forSurvey($survey),
            'suggestions' => $survey->responses()->whereNotNull('suggestion')->where('suggestion', '!=', '')->latest('submitted_at')->take(30)->pluck('suggestion'),
        ]);
    }

    public function export(Survey $survey, SurveyStatistics $statistics): BinaryFileResponse
    {
        $survey->load('questions');
        $stats = $statistics->forSurvey($survey);

        // Sheet 1: ringkasan per pertanyaan
        $summary = [
            ['Laporan Hasil Survei', $survey->title],
            ['Status', $survey->status_label],
            ['Total Responden', $stats['total']],
            ['Skor Rata-rata (1-5)', $stats['average']],
            ['Indeks Kepuasan (0-100)', $stats['index']],
            ['Kategori', $stats['label']],
            ['Diekspor pada', now()->translatedFormat('d F Y H:i')],
            [],
            ['No', 'Pertanyaan', 'Tipe', 'Jumlah Jawaban', 'Rata-rata', 'Indeks', 'Rincian'],
        ];

        foreach ($stats['questions'] as $i => $q) {
            $detail = match ($q['model']->question_type) {
                'rating' => collect($q['distribution'])->map(fn ($c, $v) => "{$v}★: {$c}")->implode(' | '),
                'multiple_choice' => collect($q['options'])->map(fn ($c, $opt) => "{$opt}: {$c}")->implode(' | '),
                default => $q['texts']->count().' jawaban teks',
            };
            $summary[] = [
                $i + 1,
                $q['model']->question_text,
                $q['model']->type_label,
                $q['answered'],
                $q['average'] ?? '',
                $q['index'] ?? '',
                $detail,
            ];
        }

        // Sheet 2: data mentah seluruh responden
        $header = ['No', 'Tanggal', 'No. Pelanggan', 'Nama Pelanggan', 'Kecamatan'];
        foreach ($survey->questions as $i => $question) {
            $header[] = 'P'.($i + 1).'. '.Str::limit($question->question_text, 80);
        }
        array_push($header, 'Skor Rata-rata', 'Kategori', 'Saran / Masukan');

        $raw = [$header];
        $survey->responses()->with(['customer', 'answers'])->orderBy('submitted_at')->chunk(500, function ($responses) use (&$raw, $survey) {
            foreach ($responses as $response) {
                $answers = $response->answers->keyBy('question_id');
                $row = [
                    count($raw),
                    $response->submitted_at?->format('d/m/Y H:i'),
                    $response->customer?->customer_number,
                    $response->customer?->full_name,
                    $response->customer?->district,
                ];
                foreach ($survey->questions as $question) {
                    $answer = $answers->get($question->id);
                    $row[] = $question->question_type === 'rating' ? $answer?->answer_value : $answer?->answer_text;
                }
                array_push($row, $response->average_score, SurveyResponse::satisfactionLabel($response->average_score), $response->suggestion);
                $raw[] = $row;
            }
        });

        // Sheet 3: per kecamatan
        $districts = [['Kecamatan', 'Jumlah Responden', 'Skor Rata-rata', 'Indeks Kepuasan']];
        foreach ($stats['districts'] as $d) {
            $districts[] = [$d['district'], $d['total'], $d['average'], $d['index']];
        }

        $path = storage_path('app/private/export-'.Str::uuid().'.xlsx');
        @mkdir(dirname($path), 0775, true);

        (new XlsxWriter())
            ->addSheet('Ringkasan', $summary, 9)
            ->addSheet('Data Responden', $raw, 1)
            ->addSheet('Per Kecamatan', $districts, 1)
            ->save($path);

        AdminActivityLog::record('Mengekspor hasil survei "'.$survey->title.'"', $survey);

        return response()
            ->download($path, 'hasil-survei-'.Str::slug($survey->title).'-'.now()->format('Ymd').'.xlsx')
            ->deleteFileAfterSend();
    }

    public function response(Survey $survey, SurveyResponse $response): View
    {
        $response->load(['customer', 'answers']);
        $survey->load('questions');

        return view('admin.surveys.response', [
            'survey' => $survey,
            'response' => $response,
            'answers' => $response->answers->keyBy('question_id'),
        ]);
    }

    public function destroyResponse(Survey $survey, SurveyResponse $response): RedirectResponse
    {
        AdminActivityLog::record('Menghapus jawaban survei "'.$survey->title.'" dari '.($response->customer?->full_name ?? 'pelanggan'), $response);
        $response->delete();

        return redirect()->route('admin.surveys.results', $survey)->with('success', 'Jawaban responden berhasil dihapus.');
    }
}
