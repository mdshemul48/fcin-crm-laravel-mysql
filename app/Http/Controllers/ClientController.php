<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Package;
use App\Models\User;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('clients.show', compact('client', 'users', 'smsTemplates'));
    }
}
