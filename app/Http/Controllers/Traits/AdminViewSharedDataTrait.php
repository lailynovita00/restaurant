<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\Auth;
use App\Models\LoyaltyExclusion;
use App\Models\Order;
use App\Models\SiteSetting;

trait AdminViewSharedDataTrait
{
    public function shareAdminViewData()
    {
        //logged-in user data 
        $loggedInUser   =    Auth::user();

        // Fetch or create site settings
        $site_settings = SiteSetting::firstOrCreate([], [
            'country' => config('site.country'),
            'currency_symbol' => config('site.currency_symbol'),
            'currency_code' => config('site.currency_code'),
        ]);

        $loyalty_eligible_orders_count = $this->calculateLoyaltyEligibleCount();

     

        view()->share([
            'loggedInUser' => $loggedInUser,
            'site_settings' => $site_settings,
            'loyalty_eligible_orders_count' => $loyalty_eligible_orders_count,
        ]);
   
    }

    protected function calculateLoyaltyEligibleCount(): int
    {
        $loyaltyOrders = Order::query()
            ->where('status', '!=', 'cancelled')
            ->with(['customer:id,phone_number'])
            ->select(['id', 'customer_phone', 'online_customer_phone', 'total_price', 'delivery_fee', 'created_at'])
            ->get();

        $loyaltyTotalsByKey = [];
        $excludedKeyTimestamps = LoyaltyExclusion::query()
            ->get(['exclusion_key', 'updated_at'])
            ->mapWithKeys(fn (LoyaltyExclusion $item) => [$item->exclusion_key => $item->updated_at])
            ->all();

        foreach ($loyaltyOrders as $order) {
            $orderTotal = (float) $order->total_price + (float) ($order->delivery_fee ?? 0);
            $rawPhone = (string) ($order->customer_phone ?: $order->online_customer_phone ?: ($order->customer?->phone_number ?? ''));
            $digits = preg_replace('/\D+/', '', $rawPhone) ?? '';

            if ($digits === '') {
                if ($orderTotal >= 1000) {
                    $exclusionKey = 'order:' . $order->id;
                    $excludedAt = $excludedKeyTimestamps[$exclusionKey] ?? null;
                    if ($excludedAt && !$order->created_at?->gt($excludedAt)) {
                        continue;
                    }

                    $loyaltyTotalsByKey[$exclusionKey] = $orderTotal;
                }

                continue;
            }

            if (str_starts_with($digits, '0')) {
                $normalizedPhone = '20' . substr($digits, 1);
            } elseif (str_starts_with($digits, '20')) {
                $normalizedPhone = $digits;
            } else {
                $normalizedPhone = $digits;
            }

            $exclusionKey = 'phone:' . $normalizedPhone;
            $excludedAt = $excludedKeyTimestamps[$exclusionKey] ?? null;
            if ($excludedAt && !$order->created_at?->gt($excludedAt)) {
                continue;
            }

            $loyaltyTotalsByKey[$exclusionKey] = ($loyaltyTotalsByKey[$exclusionKey] ?? 0) + $orderTotal;
        }

        return (int) collect($loyaltyTotalsByKey)
            ->filter(fn ($total) => (float) $total >= 1000)
            ->count();
    }
}
