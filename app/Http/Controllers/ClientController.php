<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Package;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with("package", "createdBy");

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('username', 'like', "%{$search}%")
                ->orWhere('phone_number', 'like', "%{$search}%")
                ->orWhere('client_id', 'like', "%{$search}%");
        }

        $clients = $query->paginate(100);
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
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string',
            'package_id' => 'required|exists:packages,id',
            'bill_amount' => 'required|numeric|min:0',
            'billing_status' => 'nullable|boolean',
            'remarks' => 'nullable|string',
        ]);

        $clientData = $request->all();
        $clientData['created_by'] = auth()->id();

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
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string',
            'package_id' => 'required|exists:packages,id',
            'bill_amount' => 'required|numeric|min:0',
            'billing_status' => 'nullable|boolean',
            'remarks' => 'nullable|string',
        ]);

        $client->update($request->all());

        return redirect()->route('clients.index')->with('success', 'Client updated successfully!');
    }


    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }
}
