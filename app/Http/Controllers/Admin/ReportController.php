<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Survey;
use App\Services\SurveyStatistics;
use App\Support\XlsxWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function index(Request $request, SurveyStatistics $statistics): View
    {
        [$from, $to, $surveyId] = $this->filters($request);

        return view('admin.reports.index', [
            'surveys' => Survey::orderByDesc('created_at')->get(['id', 'title']),
            'overall' => $statistics->overall($from, $to, $surveyId),
            'trend' => $statistics->monthlyTrend(12, $surveyId),
            'districts' => $statistics->byDistrict($surveyId, $from, $to),
            'weakest' => $statistics->weakestAspects(10, $surveyId, $from, $to),
            'filters' => [
                'from' => $from?->toDateString(),
                'to' => $to?->toDateString(),
                'survey_id' => $surveyId,
            ],
        ]);
    }

    public function export(Request $request, SurveyStatistics $statistics): BinaryFileResponse
    {
        [$from, $to, $surveyId] = $this->filters($request);
        $overall = $statistics->overall($from, $to, $surveyId);
        $survey = $surveyId ? Survey::find($surveyId) : null;

        $summary = [
            ['Laporan Kepuasan Pelanggan PDAM Tirta Pinang'],
            ['Survei', $survey?->title ?? 'Semua survei'],
            ['Periode', ($from?->translatedFormat('d F Y') ?? 'Awal').' s.d. '.($to?->translatedFormat('d F Y') ?? 'Sekarang')],
            ['Total Respons', $overall['total']],
            ['Jumlah Responden Unik', $overall['respondents']],
            ['Skor Rata-rata (1-5)', $overall['average']],
            ['Indeks Kepuasan (0-100)', $overall['index']],
            ['Kategori', $overall['label']],
            [],
            ['Kategori Kepuasan', 'Jumlah Responden'],
        ];
        foreach ($overall['categories'] as $label => $total) {
            $summary[] = [$label, $total];
        }

        $trend = [['Bulan', 'Jumlah Respons', 'Skor Rata-rata']];
        foreach ($statistics->monthlyTrend(12, $surveyId) as $row) {
            $trend[] = [$row['label'], $row['total'], $row['average']];
        }

        $districts = [['Kecamatan', 'Jumlah Respons', 'Skor Rata-rata', 'Indeks Kepuasan']];
        foreach ($statistics->byDistrict($surveyId, $from, $to) as $row) {
            $districts[] = [$row['district'], $row['total'], $row['average'], $row['index']];
        }

        $aspects = [['Peringkat', 'Aspek / Pertanyaan', 'Survei', 'Skor Rata-rata', 'Indeks', 'Jumlah Jawaban']];
        foreach ($statistics->weakestAspects(50, $surveyId, $from, $to) as $i => $row) {
            $aspects[] = [$i + 1, $row['question'], $row['survey'], $row['average'], $row['index'], $row['total']];
        }

        $path = storage_path('app/private/laporan-'.Str::uuid().'.xlsx');
        @mkdir(dirname($path), 0775, true);

        (new XlsxWriter())
            ->addSheet('Ringkasan', $summary, 10)
            ->addSheet('Tren Bulanan', $trend)
            ->addSheet('Per Kecamatan', $districts)
            ->addSheet('Skor per Aspek', $aspects)
            ->save($path);

        AdminActivityLog::record('Mengekspor laporan kepuasan pelanggan');

        return response()->download($path, 'laporan-kepuasan-'.now()->format('Ymd').'.xlsx')->deleteFileAfterSend();
    }

    /** @return array{0:?Carbon, 1:?Carbon, 2:?int} */
    private function filters(Request $request): array
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'survey_id' => ['nullable', 'integer', 'exists:surveys,id'],
        ]);

        return [
            $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : null,
            $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : null,
            $request->filled('survey_id') ? (int) $request->survey_id : null,
        ];
    }
}
