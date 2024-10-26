<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class BarangayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brgys = Barangay::orderBy('name')->get();
        return Inertia::render('Users/Admin/Barangay/Index', compact(['brgys']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Users/Admin/Barangay/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:50', 'unique:barangays,name']]);
        Barangay::create(['name' => $request->name]);
        return to_route('admin.barangays.create')->with(['message_success' => 'Barangay created successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $brgy = Barangay::findOrFail($id);
        return Inertia::render('Users/Admin/Barangay/Edit', compact(['brgy']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $brgy = Barangay::findOrFail($id);
        $request->validate(['name' => ['required', 'string', 'max:50', Rule::unique('barangays')->ignore($id)]]);
        $brgy->update(['name' => $request->name ?? $brgy->name]);
        return back()->with(['message_success' => 'Barangay updated successfully.']);
    }

    public function delete(string $id)
    {
        $brgy = Barangay::findOrFail($id);
        return Inertia::render('Users/Admin/Barangay/Delete', compact(['brgy']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brgy = Barangay::findOrFail($id);
        $brgy->delete();
        return to_route('admin.barangays.index')->with(['message_success' => 'Barangay deleted successfully']);
    }
}
