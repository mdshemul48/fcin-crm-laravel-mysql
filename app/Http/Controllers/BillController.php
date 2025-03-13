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

    /**
     * Manually trigger the monthly billing generation
     * Only accessible to admins
     */
    public function generateMonthlyBills()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to perform this action.');
        }
        
        // Determine the billing date for the current period
        $now = now();
        $billingDate = $now->copy()->day(14);
        
        // If we're before the 14th, we can't generate bills for the current month yet
        if ($now->day < 14) {
            return redirect()->route('dashboard')->with('warning', 'Bills can only be generated on or after the 14th of the month.');
        }
        
        // We no longer check globally if bills exist for this period
        // Instead, the BillingService will check for each client individually
        
        // Generate the bills
        Billing::generateMonthlyBills(auth()->id());
        
        return redirect()->route('dashboard')->with('success', 'Monthly bills generated successfully. Any clients who already had bills for this period were skipped.');
    }
}
