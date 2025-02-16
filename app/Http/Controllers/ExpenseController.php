<?php

namespace App\Http\Controllers;

use App\Services\ExpenseService;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    public function index(Request $request)
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

            $expenses = $this->expenseService->getExpensesByDateRange($startDate, $endDate);
            $totalExpense = $this->expenseService->getTotalExpensesByDateRange($startDate, $endDate);
            $expensesByUser = $this->expenseService->getExpensesByUser($startDate, $endDate);

            return view('expenses.index', compact(
                'expenses',
                'totalExpense',
                'startDate',
                'endDate',
                'expensesByUser'
            ));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'Error loading expenses: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validateExpenseRequest($request);
            $this->expenseService->createExpense($validated, auth()->id());

            return redirect()->route('expenses.index')
                ->with('success', 'Expense added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating expense: ' . $e->getMessage());
        }
    }

    public function edit(Expense $expense)
    {
        try {
            return view('expenses.edit', compact('expense'));
        } catch (\Exception $e) {
            return redirect()->route('expenses.index')
                ->with('error', 'Error accessing expense: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Expense $expense)
    {
        try {
            $validated = $this->validateExpenseRequest($request);

            $this->expenseService->updateExpense($expense, $validated);

            return redirect()->route('expenses.index')
                ->with('success', 'Expense updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating expense: ' . $e->getMessage());
        }
    }

    public function destroy(Expense $expense)
    {
        try {
            $this->expenseService->deleteExpense($expense);

            return redirect()->route('expenses.index')
                ->with('success', 'Expense deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting expense: ' . $e->getMessage());
        }
    }

    protected function validateExpenseRequest(Request $request)
    {
        $messages = [
            'title.required' => 'The expense title is required.',
            'amount.required' => 'The expense amount is required.',
            'amount.min' => 'The amount must be greater than zero.',
            'expense_date.required' => 'The expense date is required.',
            'receipt_image.image' => 'The receipt must be an image file.',
            'receipt_image.max' => 'The receipt image must not be larger than 2MB.'
        ];

        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'receipt_image' => 'nullable|image|max:2048'
        ], $messages);
    }
}
