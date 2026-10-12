<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SurveyStatistics
{
    /**
     * Ringkasan hasil satu survei per pertanyaan.
     *
     * @return array{total:int, average:?float, index:?float, label:string, questions:Collection, timeline:Collection, districts:Collection}
     */
    public function forSurvey(Survey $survey): array
    {
        $survey->loadMissing('questions');

        $totalResponses = $survey->responses()->count();
        $average = $survey->responses()->whereNotNull('average_score')->avg('average_score');
        $average = $average !== null ? round((float) $average, 2) : null;

        $answers = SurveyAnswer::query()
            ->whereIn('question_id', $survey->questions->pluck('id'))
            ->get()
            ->groupBy('question_id');

        $questions = $survey->questions->map(function (SurveyQuestion $question) use ($answers, $totalResponses) {
            $items = $answers->get($question->id, collect());
            $result = [
                'model' => $question,
                'answered' => $items->filter(fn ($a) => $a->answer_value !== null || filled($a->answer_text))->count(),
            ];

            if ($question->question_type === 'rating') {
                $values = $items->pluck('answer_value')->filter();
                $distribution = collect(range(1, 5))->mapWithKeys(fn ($v) => [$v => $values->filter(fn ($x) => $x === $v)->count()]);
                $avg = $values->count() ? round($values->avg(), 2) : null;
                $result += [
                    'average' => $avg,
                    'index' => SurveyResponse::satisfactionIndex($avg),
                    'label' => SurveyResponse::satisfactionLabel($avg),
                    'distribution' => $distribution,
                ];
            } elseif ($question->question_type === 'multiple_choice') {
                $counts = $items->pluck('answer_text')->filter()->countBy();
                $options = collect($question->options ?? [])
                    ->mapWithKeys(fn ($opt) => [$opt => $counts->get($opt, 0)]);
                // Jawaban yang tidak lagi ada di daftar opsi tetap ditampilkan.
                foreach ($counts as $opt => $count) {
                    if (! $options->has($opt)) {
                        $options->put($opt, $count);
                    }
                }
                $result['options'] = $options;
            } else {
                $result['texts'] = $items->filter(fn ($a) => filled($a->answer_text))
                    ->sortByDesc('id')
                    ->take(30)
                    ->pluck('answer_text')
                    ->values();
            }

            return $result;
        });

        $timeline = $survey->responses()
            ->selectRaw('DATE(submitted_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn ($row) => ['label' => Carbon::parse($row->day)->translatedFormat('d M'), 'total' => (int) $row->total]);

        return [
            'total' => $totalResponses,
            'average' => $average,
            'index' => SurveyResponse::satisfactionIndex($average),
            'label' => SurveyResponse::satisfactionLabel($average),
            'questions' => $questions,
            'timeline' => $timeline,
            'districts' => $this->byDistrict($survey->id),
        ];
    }

    /** Rata-rata skor & jumlah responden per kecamatan. */
    public function byDistrict(?int $surveyId = null, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        return SurveyResponse::query()
            ->join('customers', 'customers.id', '=', 'survey_responses.customer_id')
            ->when($surveyId, fn ($q) => $q->where('survey_responses.survey_id', $surveyId))
            ->when($from, fn ($q) => $q->where('survey_responses.submitted_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('survey_responses.submitted_at', '<=', $to))
            ->selectRaw("COALESCE(customers.district, 'Tidak diketahui') as district, COUNT(*) as total, AVG(survey_responses.average_score) as average")
            ->groupBy('district')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'district' => $row->district,
                'total' => (int) $row->total,
                'average' => $row->average !== null ? round((float) $row->average, 2) : null,
                'index' => SurveyResponse::satisfactionIndex($row->average !== null ? (float) $row->average : null),
            ]);
    }

    /** Statistik keseluruhan untuk dashboard admin, laporan & landing page. */
    public function overall(?Carbon $from = null, ?Carbon $to = null, ?int $surveyId = null): array
    {
        $base = SurveyResponse::query()
            ->when($from, fn ($q) => $q->where('submitted_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('submitted_at', '<=', $to))
            ->when($surveyId, fn ($q) => $q->where('survey_id', $surveyId));

        $average = (clone $base)->whereNotNull('average_score')->avg('average_score');
        $average = $average !== null ? round((float) $average, 2) : null;

        // Sebaran kategori kepuasan berdasarkan skor rata-rata tiap responden.
        $categories = collect(SurveyQuestion::RATING_LABELS)->reverse()->mapWithKeys(fn ($label) => [$label => 0]);
        (clone $base)->whereNotNull('average_score')->pluck('average_score')->each(function ($score) use ($categories) {
            $label = SurveyResponse::satisfactionLabel((float) $score);
            $categories->put($label, $categories->get($label, 0) + 1);
        });

        return [
            'total' => (clone $base)->count(),
            'respondents' => (clone $base)->distinct('customer_id')->count('customer_id'),
            'average' => $average,
            'index' => SurveyResponse::satisfactionIndex($average),
            'label' => SurveyResponse::satisfactionLabel($average),
            'categories' => $categories,
        ];
    }

    /** Tren jumlah respons & rata-rata skor per bulan. */
    public function monthlyTrend(int $months = 6, ?int $surveyId = null): Collection
    {
        $start = now()->startOfMonth()->subMonths($months - 1);

        $rows = SurveyResponse::query()
            ->where('submitted_at', '>=', $start)
            ->when($surveyId, fn ($q) => $q->where('survey_id', $surveyId))
            ->selectRaw("DATE_FORMAT(submitted_at, '%Y-%m') as period, COUNT(*) as total, AVG(average_score) as average")
            ->groupBy('period')
            ->get()
            ->keyBy('period');

        return collect(range(0, $months - 1))->map(function ($i) use ($start, $rows) {
            $date = $start->copy()->addMonths($i);
            $row = $rows->get($date->format('Y-m'));

            return [
                'label' => $date->translatedFormat('M Y'),
                'total' => (int) ($row->total ?? 0),
                'average' => isset($row->average) ? round((float) $row->average, 2) : null,
            ];
        });
    }

    /** Peringkat pertanyaan rating dengan skor terendah (area yang perlu perbaikan). */
    public function weakestAspects(int $limit = 5, ?int $surveyId = null, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        return DB::table('survey_answers')
            ->join('survey_responses', 'survey_responses.id', '=', 'survey_answers.response_id')
            ->join('survey_questions', 'survey_questions.id', '=', 'survey_answers.question_id')
            ->join('surveys', 'surveys.id', '=', 'survey_questions.survey_id')
            ->where('survey_questions.question_type', 'rating')
            ->whereNotNull('survey_answers.answer_value')
            ->when($surveyId, fn ($q) => $q->where('surveys.id', $surveyId))
            ->when($from, fn ($q) => $q->where('survey_responses.submitted_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('survey_responses.submitted_at', '<=', $to))
            ->groupBy('survey_questions.id', 'survey_questions.question_text', 'surveys.title')
            ->selectRaw('survey_questions.id, survey_questions.question_text, surveys.title as survey_title, AVG(survey_answers.answer_value) as average, COUNT(*) as total')
            ->orderBy('average')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'question' => $row->question_text,
                'survey' => $row->survey_title,
                'average' => round((float) $row->average, 2),
                'index' => SurveyResponse::satisfactionIndex((float) $row->average),
                'total' => (int) $row->total,
            ]);
    }
}
