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
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

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
}
