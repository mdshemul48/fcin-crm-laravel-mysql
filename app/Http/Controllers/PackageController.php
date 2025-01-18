<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with("createdBy")->paginate(100);
        return view('packages.index', compact('packages'));
    }

    public function create()
    {
        return view('packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'package_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        Package::create([
            'package_name' => $request->package_name,
            'price' => $request->price,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('packages.index')->with('success', 'Package created successfully.');
    }

    public function edit(Package $package)
    {
        return view('packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'package_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $package->update([
            'package_name' => $request->package_name,
            'price' => $request->price,
        ]);

        return redirect()->route('packages.index')->with('success', 'Package updated successfully.');
    }
}
