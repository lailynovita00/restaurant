<?php

namespace App\Http\Controllers\Admin;

use App\Models\Side;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Traits\AdminViewSharedDataTrait;

class SideController extends Controller
{
    use AdminViewSharedDataTrait;

    public function __construct()
    {
        $this->shareAdminViewData();
    }

    public function index()
    {
        if (!Schema::hasTable('sides')) {
            return back()->with('error', 'Side table is missing. Please run migrations first.');
        }

        $sides = Side::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.sides', compact('sides'));
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('sides')) {
            return back()->with('error', 'Side table is missing. Please run migrations first.');
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

        Side::create($validated);

        return back()->with('success', 'Side created successfully.');
    }

    public function update(Request $request, $id)
    {
        if (!Schema::hasTable('sides')) {
            return back()->with('error', 'Side table is missing. Please run migrations first.');
        }

        $side = Side::findOrFail($id);

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

        $side->update($validated);

        return back()->with('success', 'Side updated successfully.');
    }

    public function destroy($id)
    {
        if (!Schema::hasTable('sides')) {
            return back()->with('error', 'Side table is missing. Please run migrations first.');
        }

        $side = Side::findOrFail($id);
        $side->categories()->detach();
        $side->delete();

        return back()->with('success', 'Side deleted successfully.');
    }
}
