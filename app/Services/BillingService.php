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

        foreach ($clients as $client) {
            DB::transaction(function () use ($client, $created_by_id) {
                $existingBill = GeneratedBill::where('client_id', $client->id)
                    ->where('bill_type', 'monthly')
                    ->whereMonth('generated_date', Carbon::now()->month)
                    ->whereYear('generated_date', Carbon::now()->year)
                    ->first();

                if ($existingBill) {
                    return;
                }

                GeneratedBill::create([
                    'client_id' => $client->id,
                    'amount' => $client->bill_amount,
                    'bill_type' => 'monthly',
                    'generated_date' => Carbon::now()->startOfMonth(),
                    'month' => Carbon::now()->format('F'),
                    'remarks' => "Monthly bill for " . Carbon::now()->format('F'),
                    'created_by' => $created_by_id,
                ]);

                $client->due_amount += $client->bill_amount;
                $client->status = 'due';

                if ($client->current_balance >= $client->due_amount) {
                    $this->processPayment($created_by_id, $created_by_id, $client, 0, 0, 'Auto payment applied');
                }
                $client->save();
            });
        }
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
                $client->due_amount -= $totalPayment;
            } else if ($totalBill >= $totalPayment && $totalPayment <= $initialBalance + $totalPayment) {
                $amount_from_client_account = ($totalBill - $totalPayment);
                $client->due_amount -= $totalPayment + $initialBalance;
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
}
