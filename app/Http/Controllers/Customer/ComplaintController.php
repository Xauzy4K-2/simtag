<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $customer = $request->user()->customer;

        $complaints = $customer->complaints()
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customer.complaints.index', compact('complaints'));
    }

    public function create(Request $request): View
    {
        return view('customer.complaints.create', ['customer' => $request->user()->customer]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', Rule::in(Complaint::CATEGORIES)],
            'subject' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'location' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [], [
            'category' => 'kategori',
            'subject' => 'judul pengaduan',
            'description' => 'deskripsi',
            'location' => 'lokasi',
            'attachment' => 'foto lampiran',
        ]);

        $complaint = Complaint::create([
            'ticket_number' => Complaint::generateTicket(),
            'customer_id' => $request->user()->customer_id,
            'category' => $data['category'],
            'subject' => $data['subject'],
            'description' => $data['description'],
            'location' => $data['location'] ?? null,
            'attachment_path' => $request->hasFile('attachment')
                ? $request->file('attachment')->store('complaints', 'public')
                : null,
            'status' => 'open',
        ]);

        return redirect()->route('app.complaints.show', $complaint)
            ->with('success', 'Pengaduan berhasil dikirim dengan nomor tiket '.$complaint->ticket_number.'.');
    }

    public function show(Request $request, Complaint $complaint): View
    {
        abort_unless($complaint->customer_id === $request->user()->customer_id, 404);

        return view('customer.complaints.show', compact('complaint'));
    }
}
