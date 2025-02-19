<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CanManageExpense
{
    public function handle(Request $request, Closure $next)
    {
        $expense = $request->route('expense');

        if ($expense && !$expense->canManage(Auth::user())) {
            return redirect()->route('expenses.index')
                ->with('error', 'You are not authorized to manage this expense.');
        }

        return $next($request);
    }
}
