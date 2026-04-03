<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AdminViewSharedDataTrait;
use App\Http\Controllers\Traits\OrderStatisticsTrait;
use App\Models\LoyaltyExclusion;
use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    use AdminViewSharedDataTrait;
    use OrderStatisticsTrait;

    private const LOYALTY_THRESHOLD = 1000.0;
    private const DUMMY_REWARD_LE = 50;

    public function __construct()
    {
        $this->shareAdminViewData();
        $this->shareOrderStatistics();
    }

    public function index(Request $request)
    {
        $eligibleCustomers = $this->getEligibleCustomersByPhone();

        $currencySymbol = html_entity_decode(SiteSetting::latest()->value('currency_symbol') ?? config('site.currency_symbol'));

        return view('admin.loyalty-index', [
            'eligibleCustomers' => $eligibleCustomers,
            'loyaltyThreshold' => self::LOYALTY_THRESHOLD,
            'dummyReward' => self::DUMMY_REWARD_LE,
            'currencySymbol' => $currencySymbol,
        ]);
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'exclusion_key' => ['required', 'string', 'max:255'],
        ]);

        $exclusion = LoyaltyExclusion::firstOrCreate([
            'exclusion_key' => $validated['exclusion_key'],
        ]);
        $exclusion->touch();

        return redirect()
            ->route('admin.loyalty.index')
            ->with('success', 'Eligible customer has been removed from loyalty list.');
    }

    public function destroyAll(Request $request)
    {
        $eligibleCustomers = $this->getEligibleCustomersByPhone();

        if ($eligibleCustomers->isEmpty()) {
            return redirect()
                ->route('admin.loyalty.index')
                ->with('success', 'No eligible customers to remove.');
        }

        $now = now();
        $payload = $eligibleCustomers
            ->map(fn ($entry) => [
                'exclusion_key' => $entry['exclusion_key'],
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->values()
            ->all();

        LoyaltyExclusion::upsert($payload, ['exclusion_key'], ['updated_at']);

        return redirect()
            ->route('admin.loyalty.index')
            ->with('success', 'All eligible customers have been removed from loyalty list.');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'exclusion_key' => ['required', 'string', 'max:255'],
            'wa_phone' => ['required', 'string', 'max:20'],
        ]);

        $waPhone = preg_replace('/\D+/', '', (string) $validated['wa_phone']) ?? '';

        if ($waPhone === '') {
            return redirect()
                ->route('admin.loyalty.index')
                ->with('error', 'WhatsApp number is not valid.');
        }

        $exclusion = LoyaltyExclusion::firstOrCreate([
            'exclusion_key' => $validated['exclusion_key'],
        ]);
        $exclusion->touch();

        $whatsAppUrl = 'https://wa.me/' . $waPhone . '?text=' . urlencode($this->buildLoyaltyMessage());

        return redirect()->away($whatsAppUrl);
    }

    private function normalizePhoneForGrouping(?string $phoneInput): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phoneInput) ?? '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            return '20' . substr($digits, 1);
        }

        if (str_starts_with($digits, '20')) {
            return $digits;
        }

        return $digits;
    }

    private function normalizePhoneForEgyptWhatsApp(?string $phoneInput): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phoneInput) ?? '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0020')) {
            return '+' . substr($digits, 2);
        }

        if (str_starts_with($digits, '20')) {
            return '+' . $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '+20' . substr($digits, 1);
        }

        // Common local mobile format without leading zero, e.g. 10XXXXXXXX
        if (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            return '+20' . $digits;
        }

        return '+20' . $digits;
    }

    private function getEligibleCustomersByPhone()
    {
        $excludedKeyTimestamps = $this->getExcludedLoyaltyKeyTimestamps();

        $orders = Order::query()
            ->where('status', '!=', 'cancelled')
            ->with(['customer:id,first_name,last_name,phone_number'])
            ->select([
                'id',
                'order_no',
                'created_at',
                'total_price',
                'delivery_fee',
                'customer_phone',
                'online_customer_phone',
                'online_customer_name',
            ])
            ->latest('created_at')
            ->get();

        $grouped = [];

        foreach ($orders as $order) {
            $orderTotal = (float) $order->total_price + (float) ($order->delivery_fee ?? 0);
            $rawPhone = (string) ($order->customer_phone ?: $order->online_customer_phone ?: ($order->customer?->phone_number ?? ''));
            $normalizedPhone = $this->normalizePhoneForGrouping($rawPhone);

            if (empty($normalizedPhone)) {
                // Condition #1 fallback: keep standalone high-value orders even without usable phone.
                if ($orderTotal >= self::LOYALTY_THRESHOLD) {
                    $exclusionKey = 'order:' . $order->id;
                    $excludedAt = $excludedKeyTimestamps[$exclusionKey] ?? null;
                    if ($excludedAt && !$order->created_at?->gt($excludedAt)) {
                        continue;
                    }

                    $grouped[$exclusionKey] = [
                        'exclusion_key' => $exclusionKey,
                        'phone_display' => '-',
                        'normalized_phone' => null,
                        'customer_name' => $this->resolveCustomerName($order),
                        'orders_count' => 1,
                        'total_spent' => $orderTotal,
                        'last_order_no' => $order->order_no,
                        'last_order_at' => $order->created_at,
                    ];
                }

                continue;
            }

            $exclusionKey = 'phone:' . $normalizedPhone;
            $excludedAt = $excludedKeyTimestamps[$exclusionKey] ?? null;
            if ($excludedAt && !$order->created_at?->gt($excludedAt)) {
                continue;
            }

            if (!isset($grouped[$exclusionKey])) {
                $grouped[$exclusionKey] = [
                    'exclusion_key' => $exclusionKey,
                    'phone_display' => $rawPhone !== '' ? $rawPhone : $normalizedPhone,
                    'normalized_phone' => $normalizedPhone,
                    'customer_name' => $this->resolveCustomerName($order),
                    'orders_count' => 0,
                    'total_spent' => 0.0,
                    'last_order_no' => $order->order_no,
                    'last_order_at' => $order->created_at,
                ];
            }

            $grouped[$exclusionKey]['orders_count']++;
            $grouped[$exclusionKey]['total_spent'] += $orderTotal;

            if (
                $grouped[$exclusionKey]['last_order_at'] === null
                || $order->created_at?->gt($grouped[$exclusionKey]['last_order_at'])
            ) {
                $grouped[$exclusionKey]['last_order_at'] = $order->created_at;
                $grouped[$exclusionKey]['last_order_no'] = $order->order_no;
                $grouped[$exclusionKey]['customer_name'] = $this->resolveCustomerName($order);
                if ($rawPhone !== '') {
                    $grouped[$exclusionKey]['phone_display'] = $rawPhone;
                }
            }
        }

        return collect($grouped)
            ->filter(fn ($entry) => (float) $entry['total_spent'] >= self::LOYALTY_THRESHOLD)
            ->sortByDesc('total_spent')
            ->values()
            ->map(function ($entry) {
                $message = $this->buildLoyaltyMessage();
                $egyptWhatsappPhone = $this->normalizePhoneForEgyptWhatsApp($entry['phone_display'] ?? null);
                $waDigitsOnlyPhone = !empty($egyptWhatsappPhone)
                    ? preg_replace('/\D+/', '', $egyptWhatsappPhone)
                    : null;

                $entry['wa_phone'] = $waDigitsOnlyPhone;

                $entry['whatsapp_url'] = !empty($waDigitsOnlyPhone)
                    ? 'https://wa.me/' . $waDigitsOnlyPhone . '?text=' . urlencode($message)
                    : null;

                return $entry;
            });
    }

    private function getExcludedLoyaltyKeyTimestamps(): array
    {
        return LoyaltyExclusion::query()
            ->get(['exclusion_key', 'updated_at'])
            ->mapWithKeys(fn (LoyaltyExclusion $item) => [$item->exclusion_key => $item->updated_at])
            ->all();
    }

    private function buildLoyaltyMessage(): string
    {
        return "Hi, thank you for your loyalty at Palombini. You are eligible for a free shopping reward of " . self::DUMMY_REWARD_LE . " LE on your next order."
            . "\n\n"
            . "شكراً لولائك مع بالومبيني. إنت مؤهل للحصول على مكافأة تسوق مجانية بقيمة " . self::DUMMY_REWARD_LE . " جنيه مصري في طلبك القادم.";
    }

    private function resolveCustomerName($order): string
    {
        if (!empty($order->online_customer_name)) {
            return (string) $order->online_customer_name;
        }

        $first = trim((string) ($order->customer?->first_name ?? ''));
        $last = trim((string) ($order->customer?->last_name ?? ''));
        $combined = trim($first . ' ' . $last);

        if ($combined !== '') {
            return $combined;
        }

        return '-';
    }
}
