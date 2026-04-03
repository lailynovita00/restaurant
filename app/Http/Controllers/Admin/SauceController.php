<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sauce;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Traits\AdminViewSharedDataTrait;

class SauceController extends Controller
{
    use AdminViewSharedDataTrait;

    public function __construct()
    {
        $this->shareAdminViewData();
    }

    public function index()
    {
        if (! Schema::hasTable('sauces')) {
            return back()->with('error', 'Sauce table is missing. Please run migrations first.');
        }

        $sauces = Sauce::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.sauces', compact('sauces'));
    }

    public function store(Request $request)
    {
        if (! Schema::hasTable('sauces')) {
            return back()->with('error', 'Sauce table is missing. Please run migrations first.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['name'] = ucwords($validated['name']);
        $validated['name_ar'] = $validated['name_ar'] ?? null;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        Sauce::create($validated);

        return back()->with('success', 'Sauce created successfully.');
    }

    public function update(Request $request, $id)
    {
        if (! Schema::hasTable('sauces')) {
            return back()->with('error', 'Sauce table is missing. Please run migrations first.');
        }

        $sauce = Sauce::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['name'] = ucwords($validated['name']);
        $validated['name_ar'] = $validated['name_ar'] ?? null;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        $sauce->update($validated);

        return back()->with('success', 'Sauce updated successfully.');
    }

    public function destroy($id)
    {
        if (! Schema::hasTable('sauces')) {
            return back()->with('error', 'Sauce table is missing. Please run migrations first.');
        }

        $sauce = Sauce::findOrFail($id);
        $sauce->categories()->detach();
        $sauce->delete();

        return back()->with('success', 'Sauce deleted successfully.');
    }
}
