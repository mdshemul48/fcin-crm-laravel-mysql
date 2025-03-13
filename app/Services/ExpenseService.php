<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExpenseService
{
    public function createExpense($data, $userId)
    {
        $expense = new Expense($data);
        $expense->created_by_id = $userId;

        if (isset($data['receipt_image'])) {
            $path = $data['receipt_image']->store('expenses/receipts', 'public');
            $expense->receipt_image = $path;
        }

        $expense->save();
        return $expense;
    }

    public function updateExpense(Expense $expense, $data)
    {
        if (isset($data['receipt_image'])) {
            // Delete old image if exists
            if ($expense->receipt_image) {
                Storage::disk('public')->delete($expense->receipt_image);
            }
            $data['receipt_image'] = $data['receipt_image']->store('expenses/receipts', 'public');
        }

        return $expense->update($data);
    }

    public function getExpensesByDateRange($startDate, $endDate)
    {
        return Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->with('createdBy')
            ->latest()
            ->get();
    }

    public function getCurrentMonthExpenses()
    {
        $now = Carbon::now();
        $startDate = $now->copy()->day(14);
        
        // If we're before the 14th of this month, the billing period started on the 14th of last month
        if ($now->day < 14) {
            $startDate->subMonth();
        }
        
        $endDate = $startDate->copy()->addMonth();

        return $this->getExpensesByDateRange($startDate, $endDate);
    }

    public function getTotalExpensesByDateRange($startDate, $endDate)
    {
        return Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');
    }

    public function deleteExpense($expense)
    {
        if ($expense->receipt_image) {
            Storage::disk('public')->delete($expense->receipt_image);
        }
        return $expense->delete();
    }

    public function getCurrentMonthTotalExpenses()
    {
        $now = Carbon::now();
        $startDate = $now->copy()->day(14);
        
        // If we're before the 14th of this month, the billing period started on the 14th of last month
        if ($now->day < 14) {
            $startDate->subMonth();
        }
        
        $endDate = $startDate->copy()->addMonth();
        
        return $this->getTotalExpensesByDateRange($startDate, $endDate);
    }

    public function getPreviousMonthTotalExpenses()
    {
        $now = Carbon::now();
        $endDate = $now->copy()->day(14);
        
        // If we're before the 14th of this month, the previous period ended on the 14th of last month
        if ($now->day < 14) {
            $endDate->subMonth();
        }
        
        $startDate = $endDate->copy()->subMonth();
        
        return $this->getTotalExpensesByDateRange($startDate, $endDate);
    }

    public function getExpensesByUser($startDate, $endDate)
    {
        return Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->with('createdBy')
            ->selectRaw('created_by_id, SUM(amount) as total_amount, COUNT(*) as count')
            ->groupBy('created_by_id')
            ->get();
    }
}
