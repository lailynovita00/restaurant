<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AdminViewSharedDataTrait;
use App\Http\Controllers\Traits\OrderStatisticsTrait;
use App\Models\Menu;
use App\Models\StockItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class StockController extends Controller
{
    use AdminViewSharedDataTrait;
    use OrderStatisticsTrait;

    public function __construct()
    {
        $this->shareAdminViewData();
        $this->shareOrderStatistics();
    }

    public function index()
    {
        $this->ensureAuthorized();

        $stockItems = StockItem::query()
            ->withCount('menus')
            ->orderBy('name')
            ->get();

        $menus = Menu::query()
            ->with(['category', 'stockItems' => function ($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        $unitOptions = StockItem::unitOptions();

        return view('admin.stocks-index', compact('stockItems', 'menus', 'unitOptions'));
    }

    public function getStockData()
    {
        $this->ensureAuthorized();

        $stockItems = StockItem::query()
            ->withCount('menus')
            ->orderBy('name')
            ->get();

        $menus = Menu::query()
            ->with(['category', 'stockItems' => function ($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        $stockSummary = [
            'total_items' => $stockItems->count(),
            'healthy_items' => $stockItems->filter(fn ($item) => (float) $item->current_quantity > 5)->count(),
            'low_items' => $stockItems->filter(fn ($item) => (float) $item->current_quantity > 0 && (float) $item->current_quantity <= 5)->count(),
            'out_items' => $stockItems->filter(fn ($item) => (float) $item->current_quantity <= 0)->count(),
        ];

        $stockChartItems = $stockItems
            ->sortByDesc(fn ($item) => (float) $item->current_quantity)
            ->take(10)
            ->map(function ($item) {
                $quantity = (float) $item->current_quantity;
                return [
                    'label' => $item->name . ' (' . $item->unit_label . ')',
                    'quantity' => $quantity,
                    'status' => $quantity <= 0 ? 'out' : ($quantity <= 5 ? 'low' : 'ok'),
                ];
            })->values();

        $lowStockReport = $stockItems
            ->filter(fn ($item) => (float) $item->current_quantity <= 5)
            ->sortBy('current_quantity')
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'unit_label' => $item->unit_label,
                    'current_quantity' => (float) $item->current_quantity,
                ];
            })
            ->values();

        $stockTable = $stockItems->map(function ($item) {
            $stockValue = (float) $item->current_quantity;
            $stockStatus = $stockValue <= 0 ? 'out' : ($stockValue <= 5 ? 'low' : 'ok');
            return [
                'id' => $item->id,
                'name' => $item->name,
                'current_quantity' => $stockValue,
                'unit_label' => $item->unit_label,
                'menus_count' => $item->menus_count,
                'status' => $stockStatus,
            ];
        })->values();

        return response()->json([
            'stockSummary' => $stockSummary,
            'stockChartItems' => $stockChartItems,
            'lowStockReport' => $lowStockReport,
            'stockTable' => $stockTable,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAuthorized();

        $validated = $this->validateStockItem($request);
        StockItem::create($validated);

        return redirect()->route('admin.stocks.index')->with('success', 'Stock item created successfully.');
    }

    public function update(Request $request, StockItem $stock): RedirectResponse
    {
        $this->ensureAuthorized();

        $validated = $this->validateStockItem($request);
        $stock->update($validated);

        return redirect()->route('admin.stocks.index')->with('success', 'Stock item updated successfully.');
    }

    public function destroy(StockItem $stock): RedirectResponse
    {
        $this->ensureAuthorized();

        DB::transaction(function () use ($stock) {
            $stock->menus()->detach();
            $stock->delete();
        });

        return redirect()->route('admin.stocks.index')->with('success', 'Stock item deleted successfully.');
    }

    public function syncRecipe(Request $request, Menu $menu)
    {
        $this->ensureAuthorized();

        if ($request->boolean('clear_recipe')) {
            $menu->stockItems()->sync([]);

            return redirect()->route('admin.stocks.index')->with('success', 'Menu stock usage updated successfully.');
        }

        $ingredients = $this->normalizeIngredients($request->input('ingredients', []));
        
        Log::info('Recipe sync request:', [
            'menu_id' => $menu->id,
            'menu_name' => $menu->name,
            'raw_ingredients' => $ingredients,
        ]);
        
        // Filter out empty ingredients before validation
        $filteredIngredients = [];
        if (is_array($ingredients)) {
            foreach ($ingredients as $ingredient) {
                if (!empty($ingredient['stock_item_id']) && !empty($ingredient['quantity_required'])) {
                    $filteredIngredients[] = $ingredient;
                }
            }
        }

        Log::info('Filtered ingredients:', ['filtered' => $filteredIngredients]);

        // Validate that at least one ingredient exists
        if (empty($filteredIngredients)) {
            Log::warning('No valid ingredients provided, clearing recipe');
            $menu->stockItems()->sync([]);

            if ($request->expectsJson()) {
                return response()->json(['success' => 'Menu stock usage updated successfully.']);
            }

            return redirect()->route('admin.stocks.index')
                ->with('success', 'Menu stock usage updated successfully.');
        }

        $validated = $this->validateRecipeIngredients($filteredIngredients);
        
        Log::info('Validated ingredients:', ['validated' => $validated]);

        $syncData = collect($validated)
            ->mapWithKeys(function (array $ingredient) {
                return [
                    (int) $ingredient['stock_item_id'] => [
                        'quantity_required' => (float) $ingredient['quantity_required'],
                    ],
                ];
            })
            ->all();
    Log::info('Sync data:', ['syncData' => $syncData]);

        $menu->stockItems()->sync($syncData);

        if ($request->expectsJson()) {
            return response()->json(['success' => 'Menu stock usage updated successfully.']);
        }

        return redirect()->route('admin.stocks.index')->with('success', 'Menu stock usage updated successfully.');
    }

    protected function normalizeIngredients($ingredients): array
    {
        if (! is_array($ingredients)) {
            return [];
        }

        $normalized = [];
        $pendingStockId = null;

        foreach ($ingredients as $ingredient) {
            if (! is_array($ingredient)) {
                continue;
            }

            $hasStock = array_key_exists('stock_item_id', $ingredient) && $ingredient['stock_item_id'] !== '';
            $hasQty = array_key_exists('quantity_required', $ingredient) && $ingredient['quantity_required'] !== '';

            if ($hasStock && $hasQty) {
                $normalized[] = [
                    'stock_item_id' => $ingredient['stock_item_id'],
                    'quantity_required' => $ingredient['quantity_required'],
                ];
                $pendingStockId = null;
                continue;
            }

            if ($hasStock) {
                $pendingStockId = $ingredient['stock_item_id'];
                continue;
            }

            if ($hasQty && $pendingStockId !== null) {
                $normalized[] = [
                    'stock_item_id' => $pendingStockId,
                    'quantity_required' => $ingredient['quantity_required'],
                ];
                $pendingStockId = null;
            }
        }

        return $normalized;
    }

    protected function validateRecipeIngredients(array $ingredients): array
    {
        return collect($ingredients)
            ->map(function (array $ingredient) {
                return [
                    'stock_item_id' => (int) ($ingredient['stock_item_id'] ?? null),
                    'quantity_required' => (float) ($ingredient['quantity_required'] ?? null),
                ];
            })
            ->filter(fn (array $ing) => $ing['stock_item_id'] > 0 && $ing['quantity_required'] > 0)
            ->values()
            ->all();
    }

    protected function validateStockItem(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'current_quantity' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', Rule::in(array_keys(StockItem::unitOptions()))],
        ]);
    }

    protected function ensureAuthorized(): void
    {
        abort_unless(in_array(Auth::user()?->role, ['global_admin', 'cashier'], true), 403);
    }
}
