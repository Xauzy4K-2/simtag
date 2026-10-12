<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Daftar pembayaran (dengan filter status).
     */
    public function index(Request $request)
    {
        $payments = Payment::query()
            ->with(['customer', 'bill'])
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('payments.index', compact('payments'));
    }

    /**
     * Form input pembayaran baru untuk sebuah tagihan.
     */
    public function create(Request $request)
    {
        $bills = Bill::with('customer')
            ->whereIn('status', ['unpaid', 'overdue'])
            ->latest()
            ->get();

        return view('payments.create', compact('bills'));
    }

    /**
     * Simpan pembayaran baru (status default: pending).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bill_id'        => ['required', 'exists:bills,id'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'max:50'],
        ]);

        $bill = Bill::findOrFail($validated['bill_id']);

        $validated['customer_id']        = $bill->customer_id;
        $validated['payment_reference']  = 'PAY-' . strtoupper(Str::random(10));
        $validated['status']             = 'pending';

        $payment = Payment::create($validated);

        return redirect()
            ->route('payments.show', $payment)
            ->with('success', 'Pembayaran berhasil dicatat, menunggu konfirmasi.');
    }

    /**
     * Detail satu pembayaran.
     */
    public function show(Payment $payment)
    {
        $payment->load(['bill', 'customer']);

        return view('payments.show', compact('payment'));
    }

    /**
     * Hapus pembayaran (hanya yang masih pending/failed, sebaiknya dicegah kalau sudah success).
     */
    public function destroy(Payment $payment)
    {
        if ($payment->status === 'success') {
            return back()->with('error', 'Pembayaran yang sudah sukses tidak bisa dihapus.');
        }

        $payment->delete();

        return redirect()
            ->route('payments.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }

    /**
     * Konfirmasi pembayaran sukses, otomatis update status tagihan menjadi paid.
     */
    public function confirm(Payment $payment)
    {
        if ($payment->status === 'success') {
            return back()->with('error', 'Pembayaran ini sudah dikonfirmasi sebelumnya.');
        }

        $payment->update([
            'status'  => 'success',
            'paid_at' => now(),
        ]);

        $payment->bill->update(['status' => 'paid']);

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi & tagihan ditandai lunas.');
    }

    /**
     * Tandai pembayaran gagal.
     */
    public function markFailed(Payment $payment)
    {
        $payment->update(['status' => 'failed']);

        return back()->with('success', 'Pembayaran ditandai gagal.');
    }
}
