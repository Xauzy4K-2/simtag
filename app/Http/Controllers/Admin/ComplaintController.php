<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $complaints = Complaint::query()
            ->with(['customer', 'handler'])
            ->when($request->search, function ($query, $search) {
                $query->where(fn ($q) => $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('full_name', 'like', "%{$search}%")
                        ->orWhere('customer_number', 'like', "%{$search}%")));
            })
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->category, fn ($q, $category) => $q->where('category', $category))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = Complaint::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.complaints.index', compact('complaints', 'counts'));
    }

    public function show(Complaint $complaint): View
    {
        $complaint->load(['customer', 'handler']);

        return view('admin.complaints.show', compact('complaint'));
    }

    public function update(Request $request, Complaint $complaint): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Complaint::STATUSES))],
            'admin_response' => ['nullable', 'required_if:status,resolved,closed', 'string', 'max:5000'],
        ], [
            'admin_response.required_if' => 'Tanggapan wajib diisi saat menyelesaikan atau menutup pengaduan.',
        ], ['admin_response' => 'tanggapan']);

        $complaint->update([
            ...$data,
            'handled_by' => $request->user()->id,
            'handled_at' => now(),
        ]);

        AdminActivityLog::record('Menanggapi pengaduan '.$complaint->ticket_number.' ('.$complaint->status_label.')', $complaint);

        return back()->with('success', 'Pengaduan berhasil diperbarui.');
    }

    public function destroy(Complaint $complaint): RedirectResponse
    {
        if ($complaint->attachment_path) {
            Storage::disk('public')->delete($complaint->attachment_path);
        }

        AdminActivityLog::record('Menghapus pengaduan '.$complaint->ticket_number, $complaint);
        $complaint->delete();

        return redirect()->route('admin.complaints.index')->with('success', 'Pengaduan berhasil dihapus.');
    }
}
