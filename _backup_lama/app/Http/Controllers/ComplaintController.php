<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ComplaintController extends Controller
{
    /**
     * Daftar aduan (dengan filter status).
     */
    public function index(Request $request)
    {
        $complaints = Complaint::query()
            ->with(['customer', 'handledBy'])
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('complaints.index', compact('complaints'));
    }

    /**
     * Form buat aduan baru (mis. diinput admin atas nama pelanggan).
     */
    public function create()
    {
        $customers = Customer::active()->orderBy('full_name')->get();

        return view('complaints.create', compact('customers'));
    }

    /**
     * Simpan aduan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'    => ['required', 'exists:customers,id'],
            'subject'        => ['required', 'string', 'max:200'],
            'description'    => ['required', 'string'],
            'attachment_url' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['status'] = 'open';

        $complaint = Complaint::create($validated);

        return redirect()
            ->route('complaints.show', $complaint)
            ->with('success', 'Aduan berhasil dibuat.');
    }

    /**
     * Detail satu aduan.
     */
    public function show(Complaint $complaint)
    {
        $complaint->load(['customer', 'handledBy']);

        return view('complaints.show', compact('complaint'));
    }

    /**
     * Form edit aduan (subjek/deskripsi, sebelum ditangani).
     */
    public function edit(Complaint $complaint)
    {
        return view('complaints.edit', compact('complaint'));
    }

    /**
     * Update data dasar aduan.
     */
    public function update(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'subject'        => ['required', 'string', 'max:200'],
            'description'    => ['required', 'string'],
            'attachment_url' => ['nullable', 'string', 'max:500'],
        ]);

        $complaint->update($validated);

        return redirect()
            ->route('complaints.show', $complaint)
            ->with('success', 'Aduan berhasil diperbarui.');
    }

    /**
     * Hapus aduan.
     */
    public function destroy(Complaint $complaint)
    {
        $complaint->delete();

        return redirect()
            ->route('complaints.index')
            ->with('success', 'Aduan berhasil dihapus.');
    }

    /**
     * Admin/staff menanggapi & mengubah status aduan.
     */
    public function respond(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'admin_response' => ['required', 'string'],
            'status'         => ['required', Rule::in(['in_progress', 'resolved', 'closed'])],
        ]);

        $validated['handled_by'] = auth()->id(); // TODO: pastikan auth sudah aktif
        $validated['handled_at'] = now();

        $complaint->update($validated);

        return redirect()
            ->route('complaints.show', $complaint)
            ->with('success', 'Tanggapan berhasil disimpan.');
    }
}
