<?php

use App\Models\Client;
use App\Models\Payment;
use App\Services\BillingService;
use Carbon\Carbon;


describe("Billing Service", function () {
    beforeEach(function () {
        Carbon::setTestNow('2025-01-28');

        $this->billingService = new BillingService();
        $this->client = Client::factory()->create([
            'bill_amount' => 500,
            'current_balance' => 1000,
            'due_amount' => 0,
            'status' => 'paid',
            'billing_status' => true,
        ]);
        $this->created_by_id = $this->client->createdBy->id;
        $this->collected_by_id = $this->client->createdBy->id;
    });

    it('processes auto payment when balance is sufficient', function () {
        $this->billingService->generateMonthlyBills($this->created_by_id);

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

        $this->billingService->processPayment(
            $this->created_by_id,
            $this->collected_by_id,
            $this->client,
            300,
            50,
            'Manual payment'
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

        $this->billingService->processPayment(
            $this->created_by_id,
            $this->collected_by_id,
            $this->client,
            700,
            0,
            'Manual payment with extra balance'
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

        $this->billingService->processPayment(
            $this->created_by_id,
            $this->collected_by_id,
            $this->client,
            200,
            400,
            'Partial discount with payment'
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

        $this->billingService->processPayment(
            $this->created_by_id,
            $this->collected_by_id,
            $this->client,
            300.55,
            200.44,
            'Payment with floating points'
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
});
