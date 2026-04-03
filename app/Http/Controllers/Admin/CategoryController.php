<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Sauce;
use App\Models\Side;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Traits\AdminViewSharedDataTrait;

class CategoryController extends Controller
{
    use AdminViewSharedDataTrait;

    public function __construct()
    {
        $this->shareAdminViewData();
        
    }
    
   
    public function index()
    {
        $hasSauceTables = Schema::hasTable('sauces') && Schema::hasTable('category_sauce');
        $hasSideTables = Schema::hasTable('sides') && Schema::hasTable('category_side');

        if ($hasSauceTables || $hasSideTables) {
            $relations = [];
            if ($hasSauceTables) {
                $relations[] = 'sauces';
            }
            if ($hasSideTables) {
                $relations[] = 'sides';
            }
            $categories = Category::with($relations)->get();
        } else {
            $categories = Category::all();
        }

        if ($hasSauceTables) {
            $sauces = Sauce::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        } else {
            foreach ($categories as $category) {
                $category->setRelation('sauces', collect());
            }
            $sauces = collect();
        }

        if ($hasSideTables) {
            $sides = Side::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        } else {
            foreach ($categories as $category) {
                $category->setRelation('sides', collect());
            }
            $sides = collect();
        }

        return view('admin.categories', compact('categories', 'sauces', 'sides'));
    }

    public function store(CategoryRequest $request)
    {
        $validated = $request->validated();
        $sauceIds = $validated['sauce_ids'] ?? [];
        $sideIds = $validated['side_ids'] ?? [];
        unset($validated['sauce_ids']);
        unset($validated['side_ids']);

        $category = Category::create($validated);

        if (Schema::hasTable('sauces') && Schema::hasTable('category_sauce')) {
            $category->sauces()->sync($validated['requires_sauce'] ? $sauceIds : []);
        }

        if (Schema::hasTable('sides') && Schema::hasTable('category_side')) {
            $category->sides()->sync($validated['requires_side'] ? $sideIds : []);
        }

        return redirect()->back()->with('success', 'Category created successfully.');
    }
    

    public function update(CategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $validated = $request->validated();
        $sauceIds = $validated['sauce_ids'] ?? [];
        $sideIds = $validated['side_ids'] ?? [];
        unset($validated['sauce_ids']);
        unset($validated['side_ids']);

        $category->update($validated);

        if (Schema::hasTable('sauces') && Schema::hasTable('category_sauce')) {
            $category->sauces()->sync($validated['requires_sauce'] ? $sauceIds : []);
        }

        if (Schema::hasTable('sides') && Schema::hasTable('category_side')) {
            $category->sides()->sync($validated['requires_side'] ? $sideIds : []);
        }

        return redirect()->back()->with('success', 'Category updated successfully.');
    }
    

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return redirect()->back()->with('success', 'Category deleted successfully.');
    }


}
