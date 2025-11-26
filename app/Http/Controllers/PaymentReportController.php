<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Models\User;
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
        $userSearch = $request->get('user_search', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');

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
        $users = User::where('isActive', true)->orderBy('name')->get();
        
        // Get selected user name if user search is provided
        $selectedUser = null;
        if (!empty($userSearch)) {
            $selectedUser = User::find($userSearch);
        }

        if ($reportType === 'paid') {
            $data = $this->getPaidClientsData($selectedMonth, $selectedYear, $search, $userSearch, $dateFrom, $dateTo);
        } else {
            $data = $this->getUnpaidClientsData($selectedMonth, $selectedYear, $search, $userSearch, $dateFrom, $dateTo);
        }

        return view('payment-reports.index', compact(
            'data',
            'selectedMonth',
            'selectedYear',
            'reportType',
            'search',
            'userSearch',
            'dateFrom',
            'dateTo',
            'months',
            'years',
            'users',
            'selectedUser'
        ));
    }

    /**
     * Get paid clients data for a specific month and year
     */
    private function getPaidClientsData($month, $year, $search = '', $userSearch = '', $dateFrom = '', $dateTo = '')
    {
        $query = Client::whereHas('payments', function ($q) use ($month, $year, $userSearch, $dateFrom, $dateTo) {
            $q->where('month', $month)
                ->whereYear('payment_date', $year);
            
            if (!empty($userSearch)) {
                $q->where('collected_by', $userSearch);
            }
            
            if (!empty($dateFrom)) {
                $q->whereDate('payment_date', '>=', $dateFrom);
            }
            
            if (!empty($dateTo)) {
                $q->whereDate('payment_date', '<=', $dateTo);
            }
        })->with(['payments' => function ($q) use ($month, $year, $userSearch, $dateFrom, $dateTo) {
            $q->where('month', $month)
                ->whereYear('payment_date', $year);
            
            if (!empty($userSearch)) {
                $q->where('collected_by', $userSearch);
            }
            
            if (!empty($dateFrom)) {
                $q->whereDate('payment_date', '>=', $dateFrom);
            }
            
            if (!empty($dateTo)) {
                $q->whereDate('payment_date', '<=', $dateTo);
            }
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
    private function getUnpaidClientsData($month, $year, $search = '', $userSearch = '', $dateFrom = '', $dateTo = '')
    {
        $query = Client::whereDoesntHave('payments', function ($q) use ($month, $year, $userSearch, $dateFrom, $dateTo) {
            $q->where('month', $month)
                ->whereYear('payment_date', $year);
            
            if (!empty($userSearch)) {
                $q->where('collected_by', $userSearch);
            }
            
            if (!empty($dateFrom)) {
                $q->whereDate('payment_date', '>=', $dateFrom);
            }
            
            if (!empty($dateTo)) {
                $q->whereDate('payment_date', '<=', $dateTo);
            }
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
        $userSearch = $request->get('user_search', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');

        if ($reportType === 'paid') {
            $data = $this->getPaidClientsData($selectedMonth, $selectedYear, $search, $userSearch, $dateFrom, $dateTo);
            return $this->exportPaidClients($data, $selectedMonth, $selectedYear);
        } else {
            $data = $this->getUnpaidClientsData($selectedMonth, $selectedYear, $search, $userSearch, $dateFrom, $dateTo);
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
