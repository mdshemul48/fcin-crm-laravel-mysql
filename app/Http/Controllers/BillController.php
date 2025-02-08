<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Billing;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{

    public function generate(Request $request, $clientId)
    {
        $request->validate([
            'bill_type' => 'required|string',
            'month' => 'nullable|string',
            'amount' => 'required|numeric',
            'remarks' => 'nullable|string',
        ]);

        $client = Client::findOrFail($clientId);
        $creatorId = Auth::id();
        $billType = $request->input('bill_type');
        $month = $request->input('month');
        $amount = $request->input('amount');
        $remarks = $request->input('remarks');

        Billing::generateBillManually(
            client_id: $client->id,
            created_by_id: $creatorId,
            bill_type: $billType,
            month: $month,
            amount: $amount,
            remarks: $remarks
        );

        return redirect()->route('clients.show', $client->id)->with('success', 'Bill generated successfully.');
    }

    public function revertBill($billId)
    {
        Billing::revertBill($billId);
        return back()->with('success', 'Bill reverted successfully!');
    }
}
