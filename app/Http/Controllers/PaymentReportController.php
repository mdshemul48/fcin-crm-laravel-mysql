<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentReportController extends Controller
{
    /**
     * Display the payment reports page
     */
    public function index(Request $request)
    {
        $selectedMonth = $request->get('month', now()->format('F'));
        $selectedYear = $request->get('year', now()->year);
        $reportType = $request->get('report_type', 'paid'); // 'paid' or 'unpaid'
        $search = $request->get('search', '');

        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        $years = range(2020, now()->year + 1);

        if ($reportType === 'paid') {
            $data = $this->getPaidClientsData($selectedMonth, $selectedYear, $search);
        } else {
            $data = $this->getUnpaidClientsData($selectedMonth, $selectedYear, $search);
        }

        return view('payment-reports.index', compact(
            'data',
            'selectedMonth',
            'selectedYear',
            'reportType',
            'search',
            'months',
            'years'
        ));
    }

    /**
     * Get paid clients data for a specific month and year
     */
    private function getPaidClientsData($month, $year, $search = '')
    {
        $query = Client::whereHas('payments', function ($q) use ($month, $year) {
            $q->where('month', $month)
                ->whereYear('payment_date', $year);
        })->with(['payments' => function ($q) use ($month, $year) {
            $q->where('month', $month)
                ->whereYear('payment_date', $year);
        }, 'package']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('client_id', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy('username')->get();

        // Calculate totals
        $totalClients = $clients->count();
        $totalAmount = $clients->sum(function ($client) {
            return $client->payments->sum('amount');
        });
        $totalDiscount = $clients->sum(function ($client) {
            return $client->payments->sum('discount');
        });

        return [
            'clients' => $clients,
            'totalClients' => $totalClients,
            'totalAmount' => $totalAmount,
            'totalDiscount' => $totalDiscount,
            'totalCollection' => $totalAmount + $totalDiscount
        ];
    }

    /**
     * Get unpaid clients data for a specific month and year
     */
    private function getUnpaidClientsData($month, $year, $search = '')
    {
        $query = Client::whereDoesntHave('payments', function ($q) use ($month, $year) {
            $q->where('month', $month)
                ->whereYear('payment_date', $year);
        })->with('package');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('client_id', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy('username')->get();

        // Calculate totals
        $totalClients = $clients->count();
        $totalDueAmount = $clients->sum('due_amount');
        $totalBillAmount = $clients->sum('bill_amount');

        return [
            'clients' => $clients,
            'totalClients' => $totalClients,
            'totalDueAmount' => $totalDueAmount,
            'totalBillAmount' => $totalBillAmount
        ];
    }

    /**
     * Get monthly payment statistics
     */
    public function monthlyStats(Request $request)
    {
        $year = $request->get('year', now()->year);

        $monthlyStats = Payment::selectRaw('
            month,
            COUNT(DISTINCT client_id) as paid_clients,
            SUM(amount) as total_amount,
            SUM(discount) as total_discount
        ')
            ->whereYear('payment_date', $year)
            ->groupBy('month')
            ->orderByRaw("FIELD(month, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')")
            ->get();

        return response()->json($monthlyStats);
    }

    /**
     * Export payment report as CSV
     */
    public function export(Request $request)
    {
        $selectedMonth = $request->get('month', now()->format('F'));
        $selectedYear = $request->get('year', now()->year);
        $reportType = $request->get('report_type', 'paid');
        $search = $request->get('search', '');

        if ($reportType === 'paid') {
            $data = $this->getPaidClientsData($selectedMonth, $selectedYear, $search);
            return $this->exportPaidClients($data, $selectedMonth, $selectedYear);
        } else {
            $data = $this->getUnpaidClientsData($selectedMonth, $selectedYear, $search);
            return $this->exportUnpaidClients($data, $selectedMonth, $selectedYear);
        }
    }

    private function exportPaidClients($data, $month, $year)
    {
        $filename = "paid_clients_{$month}_{$year}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Client ID', 'Username', 'Phone', 'Package', 'Amount Paid', 'Discount', 'Payment Date', 'Collected By']);

            foreach ($data['clients'] as $client) {
                foreach ($client->payments as $payment) {
                    fputcsv($file, [
                        $client->client_id,
                        $client->username,
                        $client->phone_number,
                        $client->package->name ?? 'N/A',
                        $payment->amount,
                        $payment->discount,
                        $payment->payment_date,
                        $payment->collectedBy->name ?? 'N/A'
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportUnpaidClients($data, $month, $year)
    {
        $filename = "unpaid_clients_{$month}_{$year}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Client ID', 'Username', 'Phone', 'Package', 'Due Amount', 'Bill Amount', 'Status']);

            foreach ($data['clients'] as $client) {
                fputcsv($file, [
                    $client->client_id,
                    $client->username,
                    $client->phone_number,
                    $client->package->name ?? 'N/A',
                    $client->due_amount,
                    $client->bill_amount,
                    $client->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
