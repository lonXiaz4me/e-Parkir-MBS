<?php

namespace App\Http\Controllers;

use App\Services\BillplzService;
use App\Models\Application;
use App\Models\Notification;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        return view('auth.payment');
    }

    public function store(Request $request)
    {
        $request->validate([
            'app_no' => 'required|string|exists:application,app_no',
            'payment_type' => ['required', 'string', 'in:online_transfer,card'],
            'bank_name' => ['required_if:payment_type,online_transfer', 'nullable', 'string', 'max:100'],
        ]);

        $application = Application::where('app_no', $request->app_no)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$application->isApproved()) {
            return back()->withErrors(['app_no' => 'Permohonan ini belum diluluskan.']);
        }

        $totalAmt = (float) $application->total_amount;

        $payment = Payment::updateOrCreate(
            ['app_no' => $request->app_no],
            [
                'user_id' => Auth::id(),
                'invoice_no' => Payment::where('app_no', $request->app_no)->value('invoice_no')
                    ?? 'INV-' . strtoupper(uniqid()),
                'payment_type' => $request->payment_type,   // ← restored
                'bank_name' => $request->bank_name,      // ← restored
                'total_amt' => $totalAmt,
                'payment_status' => 'pending',
            ]
        );

        // Create the bill with the gateway (server-to-server call)
        $bill = app(BillplzService::class)->createBill([
            'name' => Auth::user()->full_name,
            'email' => Auth::user()->email,
            'amount' => $totalAmt * 100, // gateway usually wants cents
            'description' => "Sewa Petak - {$application->app_no}",
            'reference_1' => $payment->invoice_no,
            'callback_url' => route('payment.webhook'),
            'redirect_url' => route('payment.index'),
        ]);

        $payment->update(['gateway_bill_id' => $bill['id']]);

        return redirect($bill['url']); // send user to gateway's hosted page
    }

    public function receipt($id)
    {
        // ── FIX #7: Eager-load the related application and user so the receipt
        //    blade has access to real data (location, parking count, owner name,
        //    payment date) instead of hardcoded placeholder values.
        $payment = Payment::with('user')
            ->where('app_no', $id)
            ->where('user_id', Auth::id())
            ->where('payment_status', 'paid')
            ->firstOrFail();

        // Load the application separately — it's linked by app_no, not a
        // foreign key on the payment model, so we query it directly.
        $application = Application::where('app_no', $payment->app_no)->first();

        $pdf = Pdf::loadView('auth.pdf.receipt', [
            'payment' => $payment,
            'application' => $application,
            'user' => Auth::user(),
        ]);

        return $pdf->download('Resit-' . $payment->invoice_no . '.pdf');
    }

    public function webhook(Request $request)
    {
        // 1. VERIFY the signature — every gateway provides some form of this
        //    (X-Signature header, HMAC, or Billplz's own X-Signature scheme).
        //    Without this check, ANYONE can POST a fake "paid" webhook.
        if (!$this->verifyBillplzSignature($request)) {
            abort(403);
        }

        $payment = Payment::where('gateway_bill_id', $request->id)->firstOrFail();

        if ($request->paid === 'true') {
            $payment->update(['payment_status' => 'paid']);
            $payment->application()->update(['app_status' => 'completed']); // adjust to your relation
            Notification::send(/* ... */);
        } else {
            $payment->update(['payment_status' => 'failed']);
        }

        return response('OK', 200);
    }
}