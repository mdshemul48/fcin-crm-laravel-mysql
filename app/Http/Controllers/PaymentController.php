<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use Auth;
use Billing;
use DB;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function store($client_id, Request $request)
    {
        $client = Client::findOrFail($client_id);

        $validatedData = $request->validate([
            'collected_by_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'payment_date' => 'required|date',
            'payment_type' => 'required|in:monthly,one_time',
            'month' => 'nullable|in:January,February,March,April,May,June,July,August,September,October,November,December',
            'remarks' => 'nullable|string',
        ]);

        Billing::processPayment(
            created_by_id: Auth::id(),
            collected_by_id: $validatedData['collected_by_id'],
            client: $client,
            paymentAmount: $validatedData['amount'],
            discount: $validatedData['discount'] ?? 0,
            remarks: $validatedData['remarks']
        );

        return back()->with('success', 'Payment added successfully!');
    }

    public function revertPayment($paymentId)
    {

        Billing::revertPayment($paymentId);
        return back()->with('success', 'Payment reverted successfully!');
    }
}
