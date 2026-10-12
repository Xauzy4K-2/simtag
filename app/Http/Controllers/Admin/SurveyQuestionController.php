<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SurveyQuestionController extends Controller
{
    public function store(Request $request, Survey $survey): RedirectResponse
    {
        $data = $this->validated($request);
        $data['display_order'] = (int) $survey->questions()->max('display_order') + 1;

        $question = $survey->questions()->create($data);
        AdminActivityLog::record('Menambah pertanyaan pada survei "'.$survey->title.'"', $question);

        return redirect()->to(route('admin.surveys.show', $survey).'#pertanyaan')->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function update(Request $request, Survey $survey, SurveyQuestion $question): RedirectResponse
    {
        $data = $this->validated($request);

        if ($question->answers()->exists() && $question->question_type !== $data['question_type']) {
            return back()->with('error', 'Tipe pertanyaan tidak dapat diubah karena sudah memiliki jawaban.');
        }

        $question->update($data);
        AdminActivityLog::record('Mengubah pertanyaan pada survei "'.$survey->title.'"', $question);

        return redirect()->to(route('admin.surveys.show', $survey).'#q-'.$question->id)->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroy(Survey $survey, SurveyQuestion $question): RedirectResponse
    {
        if ($survey->status === 'published' && $survey->questions()->count() <= 1) {
            return back()->with('error', 'Survei aktif harus memiliki minimal 1 pertanyaan.');
        }

        AdminActivityLog::record('Menghapus pertanyaan pada survei "'.$survey->title.'"', $question, $question->question_text);
        $question->delete();

        return redirect()->to(route('admin.surveys.show', $survey).'#pertanyaan')->with('success', 'Pertanyaan berhasil dihapus.');
    }

    public function move(Survey $survey, SurveyQuestion $question, string $direction): RedirectResponse
    {
        DB::transaction(function () use ($survey, $question, $direction) {
            // Normalisasi urutan agar selalu 1..n sebelum ditukar.
            $ordered = $survey->questions()->get()->values();
            $ordered->each(fn ($q, $i) => $q->display_order !== $i + 1 && $q->update(['display_order' => $i + 1]));

            $index = $ordered->search(fn ($q) => $q->id === $question->id);
            $swapIndex = $direction === 'up' ? $index - 1 : $index + 1;
            $swap = $ordered->get($swapIndex);

            if ($swap) {
                $current = $ordered[$index]->display_order;
                $ordered[$index]->update(['display_order' => $swap->display_order]);
                $swap->update(['display_order' => $current]);
            }
        });

        return redirect()->to(route('admin.surveys.show', $survey).'#q-'.$question->id);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'question_text' => ['required', 'string', 'max:1000'],
            'question_type' => ['required', Rule::in(array_keys(SurveyQuestion::TYPES))],
            'options_text' => ['nullable', 'required_if:question_type,multiple_choice', 'string', 'max:3000'],
            'is_required' => ['nullable', 'boolean'],
        ], [
            'options_text.required_if' => 'Pilihan jawaban wajib diisi untuk pertanyaan pilihan ganda.',
        ], ['question_text' => 'teks pertanyaan', 'question_type' => 'tipe pertanyaan']);

        $options = null;
        if ($data['question_type'] === 'multiple_choice') {
            $options = collect(preg_split('/\r\n|\r|\n/', $data['options_text'] ?? ''))
                ->map(fn ($o) => trim($o))
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (count($options) < 2) {
                throw \Illuminate\Validation\ValidationException::withMessages(['options_text' => 'Minimal 2 pilihan jawaban (satu per baris).']);
            }
        }

        return [
            'question_text' => $data['question_text'],
            'question_type' => $data['question_type'],
            'options' => $options,
            'is_required' => $request->boolean('is_required'),
        ];
    }
}
