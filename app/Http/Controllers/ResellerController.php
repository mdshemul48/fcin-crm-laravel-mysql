<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use Illuminate\Http\Request;

class ResellerController extends Controller
{
    public function index()
    {
        $resellers = Reseller::withCount('recharges')
            ->withSum('recharges', 'amount')
            ->withSum('recharges', 'commission')
            ->latest()
            ->paginate(10);

        return view('resellers.index', compact('resellers'));
    }

    public function create()
    {
        return view('resellers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Reseller::create($validated);

        return redirect()->route('resellers.index')->with('message', 'Reseller created successfully');
    }

    public function edit(Reseller $reseller)
    {
        return view('resellers.edit', compact('reseller'));
    }

    public function update(Request $request, Reseller $reseller)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $reseller->update($validated);

        return redirect()->route('resellers.index')->with('message', 'Reseller updated successfully');
    }

    public function destroy(Reseller $reseller)
    {
        $reseller->delete();
        return redirect()->route('resellers.index')->with('message', 'Reseller deleted successfully');
    }
}
