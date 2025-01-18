<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::paginate(100);
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|unique:clients,client_id',
            'username' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string',
            'package_id' => 'required|integer',
            'bill_amount' => 'required|numeric|min:0',
            'disabled' => 'nullable|boolean', // Add validation for the 'disabled' field
        ]);

        Client::create([
            'client_id' => $request->client_id,
            'username' => $request->username,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'package_id' => $request->package_id,
            'bill_amount' => $request->bill_amount,
            'disabled' => $request->has('disabled') ? $request->disabled : false,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string',
            'package_id' => 'required|integer',
            'bill_amount' => 'required|numeric|min:0',
            'disabled' => 'nullable|boolean',
        ]);

        $client->update($request->only([
            'username',
            'phone_number',
            'address',
            'package_id',
            'bill_amount',
            'disabled',
        ]));

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }


    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }
}
