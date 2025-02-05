<?php

use App\Models\Client;
use App\Models\Payment;
use App\Models\GeneratedBill;
use Billing;
use Carbon\Carbon;

describe("Billing Service", function () {
    beforeEach(function () {
        Carbon::setTestNow('2025-01-28');

        $this->client = Client::factory()->create([
            'bill_amount' => 500,
            'current_balance' => 1000,
            'due_amount' => 0,
            'status' => 'paid',
            'billing_status' => true,
        ]);
        $this->creatorId = $this->client->createdBy->id;
        $this->collectorId = $this->client->createdBy->id;
    });

    it('processes auto payment when balance is sufficient', function () {
        Billing::generateMonthlyBills($this->creatorId);

        $this->client->refresh();

        expect((float) $this->client->current_balance)->toBe(500.00)
            ->and((float) $this->client->due_amount)->toBe(0.00)
            ->and($this->client->status)->toBe('paid');

        $payment = Payment::where('client_id', $this->client->id)->first();

        expect($payment)->not->toBeNull()
            ->and((float) $payment->amount)->toBe(0.00)
            ->and((float) $payment->amount_from_client_account)->toBe(500.00)
            ->and($payment->payment_type)->toBe('monthly')
            ->and($payment->month)->toBe('January')
            ->and($payment->remarks)->toBe('Auto payment applied');
    });

    it('processes manual payment with balance and payment amount', function () {
        $this->client->update([
            'current_balance' => 150,
            'due_amount' => 500,
            'status' => 'due',
        ]);

        Billing::processPayment(
            created_by_id: $this->creatorId,
            collected_by_id: $this->collectorId,
            client: $this->client,
            paymentAmount: 300,
            discount: 50,
            remarks: 'Manual payment'
        );

        $this->client->refresh();

        expect((float) $this->client->current_balance)->toBe(0.00)
            ->and((float) $this->client->due_amount)->toBe(0.00)
            ->and($this->client->status)->toBe('paid');

        $payment = Payment::where('client_id', $this->client->id)->first();

        expect($payment)->not->toBeNull()
            ->and((float) $payment->amount)->toBe(300.00)
            ->and((float) $payment->amount_from_client_account)->toBe(150.00)
            ->and((float) $payment->discount)->toBe(50.00)
            ->and($payment->remarks)->toBe('Manual payment');
    });

    it('handles extra balance after payment', function () {
        $this->client->update([
            'current_balance' => 0,
            'due_amount' => 500,
            'status' => 'due',
        ]);

        Billing::processPayment(
            created_by_id: $this->creatorId,
            collected_by_id: $this->collectorId,
            client: $this->client,
            paymentAmount: 700,
            discount: 0,
            remarks: 'Manual payment with extra balance'
        );

        $this->client->refresh();

        expect((float) $this->client->current_balance)->toBe(200.00)
            ->and((float) $this->client->due_amount)->toBe(0.00)
            ->and($this->client->status)->toBe('paid');

        $payment = Payment::where('client_id', $this->client->id)->first();

        expect($payment)->not->toBeNull()
            ->and((float) $payment->amount)->toBe(700.00)
            ->and((float) $payment->amount_from_client_account)->toBe(0.00)
            ->and($payment->remarks)->toBe('Manual payment with extra balance');
    });

    it('handles partial discount and extra balance properly', function () {
        $this->client->update([
            'current_balance' => 0,
            'due_amount' => 500,
            'status' => 'due',
        ]);

        Billing::processPayment(
            created_by_id: $this->creatorId,
            collected_by_id: $this->collectorId,
            client: $this->client,
            paymentAmount: 200,
            discount: 400,
            remarks: 'Partial discount with payment'
        );

        $this->client->refresh();

        expect((float) $this->client->current_balance)->toBe(100.00)
            ->and((float) $this->client->due_amount)->toBe(0.00)
            ->and($this->client->status)->toBe('paid');

        $payment = Payment::where('client_id', $this->client->id)->first();

        expect($payment)->not->toBeNull()
            ->and((float) $payment->amount)->toBe(200.00)
            ->and((float) $payment->amount_from_client_account)->toBe(0.00)
            ->and((float) $payment->discount)->toBe(400.00)
            ->and($payment->remarks)->toBe('Partial discount with payment');
    });

    it('handles floating point amounts correctly', function () {
        $this->client->update([
            'current_balance' => 55.44,
            'due_amount' => 555.44,
            'status' => 'due',
        ]);

        Billing::processPayment(
            created_by_id: $this->creatorId,
            collected_by_id: $this->collectorId,
            client: $this->client,
            paymentAmount: 300.55,
            discount: 200.44,
            remarks: 'Payment with floating points'
        );

        $this->client->refresh();

        expect(number_format((float) $this->client->current_balance, 2))
            ->toBe('0.99')
            ->and(number_format((float) $this->client->due_amount, 2))
            ->toBe('0.00')
            ->and($this->client->status)
            ->toBe('paid');

        $payment = Payment::where('client_id', $this->client->id)->first();

        expect($payment)->not->toBeNull()
            ->and(number_format((float) $payment->amount, 2))
            ->toBe('300.55')
            ->and(number_format((float) $payment->discount, 2))
            ->toBe('200.44')
            ->and($payment->remarks)
            ->toBe('Payment with floating points');
    });

    it('can generate a manual bill', function () {
        $billType = 'one_time';
        $month = Carbon::now()->format('F');
        $amount = 150.75;
        $remarks = 'Manual bill generation test';
      
        Billing::generateBillManually(
            client_id: $this->client->id,
            created_by_id: $this->creatorId,
            bill_type: $billType,
            month: $month,
            amount: $amount,
            remarks: $remarks
        );

        $this->client->refresh();

        $generatedBill = GeneratedBill::where('client_id', $this->client->id)
            ->where('bill_type', $billType)
            ->where('month', $month)
            ->first();

        expect($generatedBill)->not->toBeNull();
        expect((float)$generatedBill->amount)->toBe($amount);
        expect($generatedBill->remarks)->toBe($remarks);
        expect((float)$this->client->due_amount)->toBe($amount);
    });

    it('can generate a manual bill and process payment for it', function () {
        $this->client->update([
            'current_balance' => 0,
            'due_amount' => 0,
            'status' => 'due',
        ]);
      
        $billType = 'one_time';
        $month = Carbon::now()->format('F');
        $amount = 150.75;
        $remarks = 'Manual bill generation test';

        Billing::generateBillManually(
            client_id: $this->client->id,
            created_by_id: $this->creatorId,
            bill_type: $billType,
            month: $month,
            amount: $amount,
            remarks: $remarks
        );

        $this->client->refresh();

        $generatedBill = GeneratedBill::where('client_id', $this->client->id)
            ->where('bill_type', $billType)
            ->where('month', $month)
            ->first();

        expect($generatedBill)->not->toBeNull();
        expect((float)$generatedBill->amount)->toBe($amount);
        expect($generatedBill->remarks)->toBe($remarks);
        expect((float)$this->client->due_amount)->toBe($amount);

        Billing::processPayment(
            created_by_id: $this->creatorId,
            collected_by_id: $this->collectorId,
            client: $this->client,
            paymentAmount: $amount,
            discount: 0,
            remarks: 'Payment for manual bill'
        );

        $this->client->refresh();

        expect((float) $this->client->current_balance)->toBe(0.00)
            ->and((float) $this->client->due_amount)->toBe(0.00)
            ->and($this->client->status)->toBe('paid');

        $payment = Payment::where('client_id', $this->client->id)->first();

        expect($payment)->not->toBeNull()
            ->and((float) $payment->amount)->toBe($amount)
            ->and((float) $payment->amount_from_client_account)->toBe(0.00)
            ->and((float) $payment->discount)->toBe(0.00)
            ->and($payment->remarks)->toBe('Payment for manual bill');
    });
});
