<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SurveyQuestionController extends Controller
{
    /**
     * Daftar pertanyaan pada satu survei.
     */
    public function index(Survey $survey)
    {
        $questions = $survey->questions()->paginate(20);

        return view('survey_questions.index', compact('survey', 'questions'));
    }

    /**
     * Form tambah pertanyaan baru untuk survei tertentu.
     */
    public function create(Survey $survey)
    {
        return view('survey_questions.create', compact('survey'));
    }

    /**
     * Simpan pertanyaan baru.
     */
    public function store(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'question_type' => ['required', Rule::in(['text', 'rating', 'multiple_choice'])],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_required'   => ['boolean'],
        ]);

        $validated['survey_id']     = $survey->id;
        $validated['display_order'] = $validated['display_order']
            ?? ($survey->questions()->max('display_order') + 1);
        $validated['is_required']   = $request->boolean('is_required');

        SurveyQuestion::create($validated);

        return redirect()
            ->route('surveys.questions.index', $survey)
            ->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    /**
     * Form edit pertanyaan.
     */
    public function edit(Survey $survey, SurveyQuestion $question)
    {
        return view('survey_questions.edit', compact('survey', 'question'));
    }

    /**
     * Update pertanyaan.
     */
    public function update(Request $request, Survey $survey, SurveyQuestion $question)
    {
        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'question_type' => ['required', Rule::in(['text', 'rating', 'multiple_choice'])],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_required'   => ['boolean'],
        ]);

        $validated['is_required'] = $request->boolean('is_required');

        $question->update($validated);

        return redirect()
            ->route('surveys.questions.index', $survey)
            ->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    /**
     * Hapus pertanyaan (beserta jawaban terkait, via cascade FK).
     */
    public function destroy(Survey $survey, SurveyQuestion $question)
    {
        $question->delete();

        return redirect()
            ->route('surveys.questions.index', $survey)
            ->with('success', 'Pertanyaan berhasil dihapus.');
    }
}
