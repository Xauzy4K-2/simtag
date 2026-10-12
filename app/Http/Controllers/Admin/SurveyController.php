<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Survey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SurveyController extends Controller
{
    public function index(Request $request): View
    {
        $surveys = Survey::query()
            ->withCount(['questions', 'responses'])
            ->withAvg('responses', 'average_score')
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.surveys.index', compact('surveys'));
    }

    public function create(): View
    {
        return view('admin.surveys.form', ['survey' => new Survey()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $survey = Survey::create([
            ...$this->validated($request),
            'status' => 'draft',
            'created_by' => $request->user()->id,
        ]);

        AdminActivityLog::record('Membuat survei "'.$survey->title.'"', $survey);

        return redirect()->route('admin.surveys.show', $survey)
            ->with('success', 'Survei berhasil dibuat. Silakan tambahkan pertanyaan.');
    }

    public function show(Survey $survey): View
    {
        $survey->load(['questions' => fn ($q) => $q->withCount('answers'), 'creator'])->loadCount('responses');

        return view('admin.surveys.show', compact('survey'));
    }

    public function edit(Survey $survey): View
    {
        return view('admin.surveys.form', compact('survey'));
    }

    public function update(Request $request, Survey $survey): RedirectResponse
    {
        $survey->update($this->validated($request));
        AdminActivityLog::record('Memperbarui survei "'.$survey->title.'"', $survey);

        return redirect()->route('admin.surveys.show', $survey)->with('success', 'Informasi survei berhasil diperbarui.');
    }

    public function destroy(Survey $survey): RedirectResponse
    {
        AdminActivityLog::record('Menghapus survei "'.$survey->title.'"', $survey);
        $survey->delete();

        return redirect()->route('admin.surveys.index')->with('success', 'Survei beserta seluruh jawabannya berhasil dihapus.');
    }

    public function publish(Survey $survey): RedirectResponse
    {
        if ($survey->questions()->count() === 0) {
            return back()->with('error', 'Tambahkan minimal 1 pertanyaan sebelum mempublikasikan survei.');
        }

        if ($survey->ends_at && $survey->ends_at->lt(today())) {
            return back()->with('error', 'Batas akhir survei sudah lewat. Ubah tanggal batas akhir terlebih dahulu.');
        }

        $survey->update(['status' => 'published', 'published_at' => $survey->published_at ?? now(), 'closed_at' => null]);
        AdminActivityLog::record('Mempublikasikan survei "'.$survey->title.'"', $survey);

        return back()->with('success', 'Survei dipublikasikan dan kini dapat diisi pelanggan.');
    }

    public function close(Survey $survey): RedirectResponse
    {
        $survey->update(['status' => 'closed', 'closed_at' => now()]);
        AdminActivityLog::record('Menutup survei "'.$survey->title.'"', $survey);

        return back()->with('success', 'Survei ditutup. Pelanggan tidak dapat mengisi lagi.');
    }

    public function duplicate(Request $request, Survey $survey): RedirectResponse
    {
        $copy = DB::transaction(function () use ($survey, $request) {
            $copy = Survey::create([
                'title' => mb_substr($survey->title.' (Salinan)', 0, 200),
                'description' => $survey->description,
                'status' => 'draft',
                'created_by' => $request->user()->id,
            ]);

            foreach ($survey->questions as $question) {
                $copy->questions()->create($question->only(['question_text', 'question_type', 'options', 'display_order', 'is_required']));
            }

            return $copy;
        });

        AdminActivityLog::record('Menduplikasi survei "'.$survey->title.'"', $copy);

        return redirect()->route('admin.surveys.show', $copy)->with('success', 'Survei berhasil diduplikasi sebagai draf.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:2000'],
            'ends_at' => ['nullable', 'date'],
        ], [], ['title' => 'judul', 'description' => 'deskripsi', 'ends_at' => 'batas akhir']);
    }
}
