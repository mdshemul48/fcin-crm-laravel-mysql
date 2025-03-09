<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Package;
use App\Models\User;
use App\Models\SmsTemplate;
use App\Models\BalanceAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with("package", "createdBy");

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('client_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('payment_status')) {
            $status = $request->input('payment_status');
            if (in_array($status, ['paid', 'due'])) {
                $query->where('status', $status);
            }
        }

        $clients = $query->paginate(100);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('clients.list.table-content', compact('clients'))->render(),
                'url' => $request->fullUrlWithQuery(['search' => $request->search, 'payment_status' => $request->payment_status])
            ]);
        }

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        $packages = Package::all();

        return view('clients.create', compact("packages"));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|unique:clients,client_id',
            'username' => 'required|string|max:255',
            'phone_number' => 'required|digits:11',
            'address' => 'required|string',
            'package_id' => 'required|exists:packages,id',
            'bill_amount' => 'required|numeric|min:0',
            'billing_status' => 'nullable|boolean',
            'remarks' => 'nullable|string',
        ]);

        $clientData = $request->all();
        $clientData['created_by'] = Auth::id();

        Client::create($clientData);

        return redirect()->route('clients.index')->with('success', 'Client created successfully!');
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $packages = Package::all();
        return view('clients.edit', compact('client', 'packages'));
    }


    public function update(Request $request, Client $client)
    {
        $request->validate([
            'client_id' => 'required|unique:clients,client_id,' . $client->id,
            'username' => 'required|string|max:255',
            'phone_number' => 'required|digits:11',
            'address' => 'required|string',
            'package_id' => 'required|exists:packages,id',
            'bill_amount' => 'required|numeric|min:0',
            'status' => 'nullable|in:due,paid',
            'remarks' => 'nullable|string',
        ]);

        $client->update($request->all());

        return redirect()->route('clients.index')->with('success', 'Client updated successfully!');
    }


    public function show(Client $client)
    {
        $users = User::all("id", "name");
        $smsTemplates = SmsTemplate::where('type', 'payment')
            ->where('is_active', true)
            ->get();
        
        // Load balance adjustments with their relationships
        $balanceAdjustments = $client->balanceAdjustments()
            ->with('adjustedBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('clients.show', compact('client', 'users', 'smsTemplates', 'balanceAdjustments'));
    }

    public function adjustBalance(Request $request, Client $client)
    {
        $request->validate([
            'adjustment_type' => 'required|in:current_balance,due_amount,both',
            'current_balance' => 'required_if:adjustment_type,current_balance,both|numeric|min:0',
            'due_amount' => 'required_if:adjustment_type,due_amount,both|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $adjustmentType = $request->adjustment_type;
        $remarks = $request->remarks ?? 'Manual balance adjustment';

        // Start a database transaction
        DB::transaction(function () use ($client, $adjustmentType, $request, $remarks) {
            $oldCurrentBalance = $client->current_balance;
            $oldDueAmount = $client->due_amount;
            
            // Adjust current balance if requested
            if ($adjustmentType === 'current_balance' || $adjustmentType === 'both') {
                $client->current_balance = $request->current_balance;
            }
            
            // Adjust due amount if requested
            if ($adjustmentType === 'due_amount' || $adjustmentType === 'both') {
                $client->due_amount = $request->due_amount;
            }
            
            // Update client status based on due amount
            if ($client->due_amount <= 0) {
                $client->status = 'paid';
            } else {
                $client->status = 'due';
            }
            
            // Check if current_balance and due_amount are equal, if so, set both to 0
            if (abs($client->current_balance - $client->due_amount) < 0.01) {
                $client->current_balance = 0;
                $client->due_amount = 0;
                $client->status = 'paid';
            }
            
            // Save the changes
            $client->save();
            
            // Save the adjustment history
            BalanceAdjustment::create([
                'client_id' => $client->id,
                'adjusted_by' => auth()->id(),
                'adjustment_type' => $adjustmentType,
                'old_current_balance' => $oldCurrentBalance,
                'new_current_balance' => $client->current_balance,
                'old_due_amount' => $oldDueAmount,
                'new_due_amount' => $client->due_amount,
                'remarks' => $remarks
            ]);
            
            // Log the adjustment
            \Log::info('Client balance adjusted', [
                'client_id' => $client->id,
                'user_id' => auth()->id(),
                'old_current_balance' => $oldCurrentBalance,
                'new_current_balance' => $client->current_balance,
                'old_due_amount' => $oldDueAmount,
                'new_due_amount' => $client->due_amount,
                'remarks' => $remarks
            ]);
        });

        return redirect()->route('clients.show', $client->id)
            ->with('success', 'Client balance adjusted successfully');
    }
}
