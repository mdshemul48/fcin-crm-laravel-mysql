<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserTransactionController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('id', '!=', auth()->id())->get();

        // Set default date range to current month if not specified
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now()->endOfMonth();

        // Base query with date range
        $query = UserTransaction::with(['fromUser', 'toUser'])
            ->whereBetween('created_at', [
                $startDate->startOfDay(),
                $endDate->endOfDay()
            ]);

        // Get paginated transactions
        $transactions = $query->latest()->paginate(15)->withQueryString();

        // Calculate totals for the filtered date range
        $myTotalReceived = (clone $query)->where('to_user_id', auth()->id())->sum('amount');
        $myTotalSent = (clone $query)->where('from_user_id', auth()->id())->sum('amount');

        return view('transactions.index', compact(
            'users',
            'transactions',
            'myTotalReceived',
            'myTotalSent',
            'startDate',
            'endDate'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        $transaction = UserTransaction::create([
            'from_user_id' => auth()->id(),
            'to_user_id' => $validated['to_user_id'],
            'amount' => $validated['amount'],
            'note' => $validated['note'],
        ]);

        // Update the created_at timestamp to the selected date
        $transaction->created_at = Carbon::parse($validated['transaction_date']);
        $transaction->updated_at = Carbon::parse($validated['transaction_date']);
        $transaction->save();

        return redirect()->route('transactions.index')->with('message', 'Transaction recorded successfully');
    }

    public function update(Request $request, UserTransaction $transaction)
    {
        // Check if user is authorized to edit this transaction
        if ($transaction->from_user_id !== auth()->id()) {
            return back()->with('error', 'You can only edit transactions you created');
        }

        $validated = $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        $transaction->update([
            'to_user_id' => $validated['to_user_id'],
            'amount' => $validated['amount'],
            'note' => $validated['note'],
        ]);

        // Update the created_at timestamp to the selected date
        $transaction->created_at = Carbon::parse($validated['transaction_date']);
        $transaction->updated_at = Carbon::parse($validated['transaction_date']);
        $transaction->save();

        return redirect()->route('transactions.index')->with('message', 'Transaction updated successfully');
    }

    public function destroy(UserTransaction $transaction)
    {
        if ($transaction->from_user_id !== auth()->id()) {
            return back()->with('error', 'You can only delete transactions you created');
        }

        $transaction->delete();

        return redirect()->route('transactions.index')->with('message', 'Transaction deleted successfully');
    }
}
