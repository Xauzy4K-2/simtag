<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SurveyController extends Controller
{
    /**
     * Daftar semua survei.
     */
    public function index(Request $request)
    {
        $surveys = Survey::query()
            ->withCount(['questions', 'answers'])
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('surveys.index', compact('surveys'));
    }

    /**
     * Form tambah survei baru.
     */
    public function create()
    {
        return view('surveys.create');
    }

    /**
     * Simpan survei baru (status default: draft).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['status'] = 'draft';
        $validated['created_by'] = auth()->id(); // TODO: pastikan auth sudah aktif

        $survey = Survey::create($validated);

        return redirect()
            ->route('surveys.show', $survey)
            ->with('success', 'Survei berhasil dibuat.');
    }

    /**
     * Detail survei beserta daftar pertanyaan.
     */
    public function show(Survey $survey)
    {
        $survey->load(['questions', 'creator']);
        $survey->loadCount('answers');

        return view('surveys.show', compact('survey'));
    }

    /**
     * Form edit survei.
     */
    public function edit(Survey $survey)
    {
        return view('surveys.edit', compact('survey'));
    }

    /**
     * Update judul/deskripsi survei.
     */
    public function update(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
        ]);

        $survey->update($validated);

        return redirect()
            ->route('surveys.show', $survey)
            ->with('success', 'Survei berhasil diperbarui.');
    }

    /**
     * Hapus survei (beserta pertanyaan & jawaban, via cascade FK).
     */
    public function destroy(Survey $survey)
    {
        $survey->delete();

        return redirect()
            ->route('surveys.index')
            ->with('success', 'Survei berhasil dihapus.');
    }

    /**
     * Publikasikan survei agar bisa diisi pelanggan.
     */
    public function publish(Survey $survey)
    {
        if ($survey->questions()->count() === 0) {
            return back()->with('error', 'Survei harus punya minimal 1 pertanyaan sebelum dipublikasikan.');
        }

        $survey->update([
            'status'       => 'published',
            'published_at' => now(),
        ]);

        return back()->with('success', 'Survei berhasil dipublikasikan.');
    }

    /**
     * Tutup survei agar tidak bisa diisi lagi.
     */
    public function close(Survey $survey)
    {
        $survey->update([
            'status'    => 'closed',
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Survei berhasil ditutup.');
    }
}
