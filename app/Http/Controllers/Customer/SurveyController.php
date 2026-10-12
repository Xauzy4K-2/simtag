<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SurveyController extends Controller
{
    public function index(Request $request): View
    {
        $customer = $request->user()->customer;
        $responses = $customer->surveyResponses()->get()->keyBy('survey_id');

        $surveys = Survey::query()
            ->where(fn ($q) => $q->where('status', 'published')->orWhereIn('id', $responses->keys()))
            ->withCount('questions')
            ->latest('published_at')
            ->get();

        return view('customer.surveys.index', [
            'available' => $surveys->filter(fn ($s) => $s->isOpen() && ! $responses->has($s->id))->values(),
            'completed' => $surveys->filter(fn ($s) => $responses->has($s->id))->values(),
            'responses' => $responses,
        ]);
    }

    public function show(Request $request, Survey $survey): View|RedirectResponse
    {
        $customer = $request->user()->customer;
        $response = $customer->surveyResponses()->where('survey_id', $survey->id)->with('answers')->first();

        if (! $response && ! $survey->isOpen()) {
            return redirect()->route('app.surveys.index')->with('error', 'Survei ini sudah tidak menerima jawaban.');
        }

        $survey->load('questions');

        return view('customer.surveys.show', [
            'survey' => $survey,
            'response' => $response,
            'answers' => $response?->answers->keyBy('question_id'),
        ]);
    }

    public function store(Request $request, Survey $survey): RedirectResponse
    {
        $customer = $request->user()->customer;

        if (! $survey->isOpen()) {
            return redirect()->route('app.surveys.index')->with('error', 'Survei ini sudah tidak menerima jawaban.');
        }

        if ($customer->surveyResponses()->where('survey_id', $survey->id)->exists()) {
            return redirect()->route('app.surveys.show', $survey)->with('error', 'Anda sudah mengisi survei ini.');
        }

        $survey->load('questions');
        $rules = ['suggestion' => ['nullable', 'string', 'max:2000']];
        $attributes = [];

        foreach ($survey->questions as $index => $question) {
            $key = "answers.{$question->id}";
            $attributes[$key] = 'pertanyaan nomor '.($index + 1);
            $rule = [$question->is_required ? 'required' : 'nullable'];

            $rules[$key] = match ($question->question_type) {
                'rating' => [...$rule, 'integer', 'between:1,5'],
                'multiple_choice' => [...$rule, 'string', Rule::in($question->options ?? [])],
                default => [...$rule, 'string', 'max:2000'],
            };
        }

        $data = $request->validate($rules, [
            'answers.*.required' => ':attribute wajib dijawab.',
        ], $attributes);

        $answers = $data['answers'] ?? [];
        $ratings = $survey->questions
            ->where('question_type', 'rating')
            ->map(fn ($q) => $answers[$q->id] ?? null)
            ->filter()
            ->map(fn ($v) => (int) $v);

        try {
            DB::transaction(function () use ($survey, $customer, $data, $answers, $ratings) {
                $response = SurveyResponse::create([
                    'survey_id' => $survey->id,
                    'customer_id' => $customer->id,
                    'suggestion' => $data['suggestion'] ?? null,
                    'average_score' => $ratings->count() ? round($ratings->avg(), 2) : null,
                    'submitted_at' => now(),
                ]);

                foreach ($survey->questions as $question) {
                    $value = $answers[$question->id] ?? null;
                    if ($value === null || $value === '') {
                        continue;
                    }

                    $response->answers()->create([
                        'question_id' => $question->id,
                        'answer_value' => $question->question_type === 'rating' ? (int) $value : null,
                        'answer_text' => $question->question_type === 'rating' ? null : trim((string) $value),
                    ]);
                }
            });
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['suggestion' => 'Anda sudah mengisi survei ini.']);
        }

        return redirect()->route('app.surveys.show', $survey)
            ->with('success', 'Terima kasih! Jawaban survei Anda berhasil dikirim.');
    }
}
