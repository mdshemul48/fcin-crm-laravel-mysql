<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $totalClients = Client::count();
        $paidClients = Client::where('status', 'paid')->count();
        $unpaidClients = Client::where('status', 'due')->count();
        $totalDue = Client::where('status', 'due')->sum('due_amount');

        $totalPaymentCollectionsThisMonth = Payment::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        $paymentCollections = Payment::selectRaw('collected_by, SUM(amount) as total_amount')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->groupBy('collected_by')
            ->with('user')
            ->get();

        return view('dashboard', compact('totalClients', 'paidClients', 'unpaidClients', 'totalDue', 'totalPaymentCollectionsThisMonth', 'paymentCollections'));
    }
}
