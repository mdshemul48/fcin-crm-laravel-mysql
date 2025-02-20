<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use App\Models\ResellerRecharge;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ResellerRechargeController extends Controller
{
    public function index(Request $request)
    {
        $resellers = Reseller::all();
        $query = ResellerRecharge::with('reseller');

        // Apply filters
        if ($request->filled('reseller_id')) {
            $query->where('reseller_id', $request->reseller_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        } else {
            // Default to current month if no dates specified
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        }

        $recharges = $query->latest()->paginate(15)->withQueryString();

        // Calculate totals for the filtered results
        $totals = $query->selectRaw('
            COUNT(*) as count,
            SUM(amount) as total_amount,
            SUM(commission) as total_commission
        ')->first();

        return view('reseller-recharges.index', compact('recharges', 'resellers', 'totals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reseller_id' => 'required|exists:resellers,id',
            'amount' => 'required|numeric|min:0',
            'commission' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        ResellerRecharge::create($validated);

        return redirect()->back()->with('message', 'Recharge added successfully');
    }

    public function edit(ResellerRecharge $resellerRecharge)
    {
        $resellers = Reseller::all();
        return view('reseller-recharges.edit', compact('resellerRecharge', 'resellers'));
    }

    public function update(Request $request, ResellerRecharge $resellerRecharge)
    {
        $validated = $request->validate([
            'reseller_id' => 'required|exists:resellers,id',
            'amount' => 'required|numeric|min:0',
            'commission' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $resellerRecharge->update($validated);

        return redirect()->route('reseller-recharges.index')->with('message', 'Recharge updated successfully');
    }

    public function destroy(ResellerRecharge $resellerRecharge)
    {
        $resellerRecharge->delete();
        return redirect()->back()->with('message', 'Recharge deleted successfully');
    }
}
