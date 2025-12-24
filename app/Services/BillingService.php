<?php

namespace App\Services;

use App\Models\Client;
use App\Models\GeneratedBill;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function generateMonthlyBills($created_by_id): void
    {
        $clients = Client::where('billing_status', true)->get();

        // Determine the billing date for the current period (first day of the month)
        $now = Carbon::now();
        $billingDate = $now->copy()->startOfMonth();

    
        // The bill is for the current month
        $billMonth = $billingDate->format('F');
        $billYear = $billingDate->year;

        // We no longer check globally if bills exist for this period
        // Instead, we'll check for each client individually

        $billsGenerated = 0;
        $clientsProcessed = 0;

        foreach ($clients as $client) {
            $clientsProcessed++;

            // Check if this specific client already has a bill for this period
            $existingBill = GeneratedBill::where('client_id', $client->id)
                ->where('bill_type', 'monthly')
                ->whereMonth('generated_date', $billingDate->month)
                ->whereYear('generated_date', $billingDate->year)
                ->first();

            // Skip this client if they already have a bill
            if ($existingBill) {
                continue;
            }

            // Generate bill for this client
            DB::transaction(function () use ($client, $created_by_id, $billingDate, $billMonth, $billYear, &$billsGenerated) {
                GeneratedBill::create([
                    'client_id' => $client->id,
                    'amount' => $client->bill_amount,
                    'bill_type' => 'monthly',
                    'generated_date' => $billingDate,
                    'month' => $billMonth,
                    'remarks' => "Monthly bill for {$billMonth} {$billYear} (1st to last day of month)",
                    'created_by' => $created_by_id,
                ]);

                $client->due_amount += $client->bill_amount;
                $client->status = 'due';

                if ($client->current_balance >= $client->due_amount) {
                    $this->processPayment($created_by_id, $created_by_id, $client, 0, 0, 'Auto payment applied');
                }
                $client->save();

                $billsGenerated++;
            });
        }

        // Log the results
        \Log::info("Monthly billing process completed: {$billsGenerated} bills generated out of {$clientsProcessed} clients processed.");
    }

    public function processPayment(
        $created_by_id,
        $collected_by_id,
        Client $client,
        float $paymentAmount = 0,
        float $discount = 0,
        ?string $remarks = null,
        string $paymentType = 'monthly',
        ?string $month = null
    ): void {
        DB::transaction(function () use ($client, $collected_by_id, $created_by_id, $paymentAmount, $discount, $remarks, $paymentType, $month) {
            $totalBill = $client->due_amount;
            $totalPayment = $paymentAmount + $discount;
            $initialBalance = $client->current_balance;
            $amount_from_client_account = 0;

            if ($totalBill <= $totalPayment) {
                $client->due_amount -= $totalPayment + $initialBalance;
            } else if ($totalBill >= $totalPayment && $totalBill <= $initialBalance + $totalPayment) {
                $amount_from_client_account = ($totalBill - $totalPayment);
                $client->due_amount -= $totalPayment + $initialBalance;
            } else {
                $client->due_amount -= $totalPayment;
            }

            if ($client->due_amount <= 0) {
                $client->status = 'paid';
                $client->current_balance = abs($client->due_amount);
                $client->due_amount = 0;
            } else {
                $client->status = 'due';
            }

            Payment::create([
                'client_id' => $client->id,
                'amount' => $paymentAmount,
                'discount' => $discount,
                'amount_from_client_account' => $amount_from_client_account,
                'payment_date' => Carbon::now(),
                'payment_type' => $paymentType,
                'month' => $month ?? Carbon::now()->format('F'),
                'remarks' => $remarks,
                'collected_by' => $collected_by_id,
                'created_by' => $created_by_id,
            ]);

            $client->save();
        });
    }

    public function generateBillManually($client_id, $created_by_id, $bill_type, $month, $amount, $remarks): void
    {
        $client = Client::find($client_id);

        DB::transaction(function () use ($client, $created_by_id, $bill_type, $month, $amount, $remarks) {
            GeneratedBill::create([
                'client_id' => $client->id,
                'amount' => $amount,
                'bill_type' => $bill_type,
                'generated_date' => Carbon::now(),
                'month' => $month,
                'remarks' => $remarks,
                'created_by' => $created_by_id,
            ]);

            $client->due_amount += $amount;
            $client->status = 'due';

            if ($client->current_balance >= $client->due_amount) {
                $this->processPayment($created_by_id, $created_by_id, $client, 0, 0, 'Auto payment applied');
            }

            $client->save();
        });
    }


    public function revertPayment($paymentId): void
    {
        $payment = Payment::find($paymentId);
        $client = $payment->client;
        DB::transaction(function () use ($client, $payment) {
            $client->due_amount += $payment->amount + $payment->discount;
            $client->status = 'due';

            if ($payment->amount_from_client_account > 0) {
                $client->current_balance += $payment->amount_from_client_account;
            }

            if (abs($client->current_balance - $client->due_amount) < 0.01) {
                $client->current_balance = 0;
                $client->due_amount = 0;
                $client->status = 'paid';
            }

            $client->save();
            $payment->delete();
        });
    }

    public function revertBill($billId): void
    {
        $bill = GeneratedBill::find($billId);
        $client = $bill->client;
        DB::transaction(function () use ($client, $bill) {
            if ($client->due_amount >= $bill->amount) {
                $client->due_amount -= $bill->amount;
            }

            if (abs($client->current_balance - $client->due_amount) < 0.01) {
                $client->current_balance = 0;
                $client->due_amount = 0;
                $client->status = 'paid';
            } else {
                $client->status = 'due';
            }

            $client->save();
            $bill->delete();
        });
    }
}
