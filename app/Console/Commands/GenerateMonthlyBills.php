<?php

namespace App\Console\Commands;

use App\Services\BillingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyBills extends Command
{
    protected $signature = 'billing:generate';
    protected $description = 'Generate monthly bills for clients.';

    protected $billingService;

    public function __construct(BillingService $billingService)
    {
        parent::__construct();
        $this->billingService = $billingService;
    }

    public function handle()
    {
        $createdById = 1;
        try {
            $this->billingService->generateMonthlyBills($createdById);
            $this->info('Monthly bills generated successfully.');
            Log::info('Monthly bills generated successfully.');
        } catch (\Exception $e) {
            $this->error('Error generating monthly bills: ' . $e->getMessage());
            Log::error('Error generating monthly bills: ' . $e->getMessage());
            return 1;
        }
    }
}
