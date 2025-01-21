<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::with("package", "createdBy")->paginate(100);
        // dd($clients);
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
            'disabled' => 'nullable|boolean',
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

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $packages = Package::all();
        return view('clients.edit', compact('client', 'packages'));
    }


    public function update(Request $request, Client $client)
    {
        $request->validate([
            'client_id' => 'required|unique:clients,client_id',
            'username' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string',
            'package_id' => 'required|exists:packages,id',
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
            'client_id',
        ]));

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }


    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }
}
