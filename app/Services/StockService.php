<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\Order;
use App\Models\StockItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function applyOrderStock(Order $order): void
    {
        $netQuantity = (float) StockMovement::query()
            ->where('order_id', $order->id)
            ->sum('quantity_change');

        if ($netQuantity < 0) {
            return;
        }

        $requirements = $this->buildRequirements($order);

        if ($requirements === []) {
            return;
        }

        DB::transaction(function () use ($order, $requirements) {
            foreach ($requirements as $stockItemId => $data) {
                $stockItem = StockItem::query()->lockForUpdate()->find($stockItemId);

                if (! $stockItem) {
                    continue;
                }

                $stockItem->decrement('current_quantity', $data['quantity']);

                StockMovement::create([
                    'stock_item_id' => $stockItem->id,
                    'order_id' => $order->id,
                    'menu_id' => $data['menu_id'],
                    'created_by_user_id' => Auth::id(),
                    'movement_type' => 'order_deduction',
                    'quantity_change' => -1 * $data['quantity'],
                    'note' => 'Auto deduction for order #' . $order->order_no,
                ]);
            }
        });
    }

    public function restoreOrderStock(Order $order, string $movementType = 'order_restore'): void
    {
        $netQuantities = StockMovement::query()
            ->where('order_id', $order->id)
            ->selectRaw('stock_item_id, SUM(quantity_change) as net_quantity')
            ->groupBy('stock_item_id')
            ->get();

        if ($netQuantities->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($order, $movementType, $netQuantities) {
            foreach ($netQuantities as $movementSummary) {
                $netQuantity = (float) $movementSummary->net_quantity;

                if ($netQuantity >= 0) {
                    continue;
                }

                $restoreQuantity = abs($netQuantity);
                $stockItem = StockItem::query()->lockForUpdate()->find($movementSummary->stock_item_id);

                if (! $stockItem) {
                    continue;
                }

                $stockItem->increment('current_quantity', $restoreQuantity);

                StockMovement::create([
                    'stock_item_id' => $stockItem->id,
                    'order_id' => $order->id,
                    'created_by_user_id' => Auth::id(),
                    'movement_type' => $movementType,
                    'quantity_change' => $restoreQuantity,
                    'note' => 'Stock restored for order #' . $order->order_no,
                ]);
            }
        });
    }

    protected function buildRequirements(Order $order): array
    {
        $order->loadMissing('orderItems');

        $orderItems = $order->orderItems;
        if ($orderItems->isEmpty()) {
            return [];
        }

        $menuIds = $orderItems->pluck('menu_id')->filter()->unique()->values();
        $menus = Menu::with('stockItems')
            ->where(function ($query) use ($menuIds, $orderItems) {
                if ($menuIds->isNotEmpty()) {
                    $query->whereIn('id', $menuIds);
                }

                $menuNames = $orderItems->pluck('menu_name')->filter()->unique()->values();
                if ($menuNames->isNotEmpty()) {
                    $query->orWhereIn('name', $menuNames)
                        ->orWhereIn('name_ar', $menuNames);
                }
            })
            ->get();

        $menusById = $menus->keyBy('id');
        $menusByName = $menus->flatMap(function (Menu $menu) {
            $pairs = [];

            if (! empty($menu->name)) {
                $pairs[$menu->name] = $menu;
            }

            if (! empty($menu->name_ar)) {
                $pairs[$menu->name_ar] = $menu;
            }

            return $pairs;
        });

        $requirements = [];

        foreach ($orderItems as $orderItem) {
            $menu = $orderItem->menu_id
                ? $menusById->get($orderItem->menu_id)
                : $menusByName->get($orderItem->menu_name);

            if (! $menu || $menu->stockItems->isEmpty()) {
                continue;
            }

            $quantityOrdered = max(1, (int) $orderItem->quantity);

            foreach ($menu->stockItems as $stockItem) {
                $requiredQuantity = (float) $stockItem->pivot->quantity_required * $quantityOrdered;

                if ($requiredQuantity <= 0) {
                    continue;
                }

                if (! isset($requirements[$stockItem->id])) {
                    $requirements[$stockItem->id] = [
                        'quantity' => 0,
                        'menu_id' => $menu->id,
                    ];
                }

                $requirements[$stockItem->id]['quantity'] += $requiredQuantity;
            }
        }

        return $requirements;
    }
}