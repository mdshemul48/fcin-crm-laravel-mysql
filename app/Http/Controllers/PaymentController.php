<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Billing;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $creatorId;
    protected $collectorId;
    protected $client;

    public function __construct()
    {
        $this->creatorId = auth()->id(); // Assuming the creator is the authenticated user
        $this->collectorId = 1; // Set this to the appropriate collector ID
        $this->client = null; // Initialize client to null or appropriate value
    }

    public function store($client_id, Request $request)
    {
        $this->client = Client::findOrFail($client_id);

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
            created_by_id: $this->creatorId,
            collected_by_id: $validatedData['collected_by_id'],
            client: $this->client,
            paymentAmount: $validatedData['amount'],
            discount: $validatedData['discount'] ?? 0,
            remarks: $validatedData['remarks']
        );

        return back()->with('success', 'Payment added successfully!');
    }
}
