<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use Auth;
use Sms;
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
            'year' => 'nullable|numeric',
            'remarks' => 'nullable|string',
            'send_sms' => 'nullable|boolean',
        ]);

        Billing::processPayment(
            created_by_id: Auth::id(),
            collected_by_id: $validatedData['collected_by_id'],
            client: $client,
            paymentAmount: $validatedData['amount'],
            discount: $validatedData['discount'] ?? 0,
            remarks: $validatedData['remarks']
        );

        // Only send SMS if the checkbox is checked
        if ($request->has('send_sms') && $request->boolean('send_sms')) {
            Sms::sendTemplatedSms(
                $client->phone_number,
                'payment',
                [
                    'client' => $client->load('package'),
                    'amount' => $validatedData['amount'],
                    'discount' => $validatedData['discount'] ?? 0,
                    'payment_date' => date('d/m/Y', strtotime($validatedData['payment_date'])),
                    'payment_type' => $validatedData['payment_type'],
                    'month' => $validatedData['month'] ?? '',
                    'year' => $validatedData['year'] ?? date('Y'),
                    'remarks' => $validatedData['remarks'] ?? ''
                ]
            );
        }

        return back()->with('success', 'Payment added successfully!');
    }

    public function revertPayment($paymentId)
    {

        Billing::revertPayment($paymentId);
        return back()->with('success', 'Payment reverted successfully!');
    }
}
