<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use App\Mail\PasswordChangedNotification;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Controllers\Traits\OrderStatisticsTrait;
use App\Http\Controllers\Traits\AdminViewSharedDataTrait;

class AdminController extends Controller
{
    use OrderStatisticsTrait;
    use AdminViewSharedDataTrait;


    public function __construct()
    {
        $this->shareAdminViewData();
        $this->shareOrderStatistics();
        
    }
    
    public function index()
    {
        $selectedReportYear = request()->integer('report_year');
        $selectedReportMonth = request()->integer('report_month');

        $soldItemsReport = $this->buildSoldItemsReportPayload(
            $selectedReportYear,
            $selectedReportMonth
        );

        $salesChartPayload = $this->buildSalesChartPayload();

        return view('admin.dashboard', compact('salesChartPayload', 'soldItemsReport'));
    }

    public function salesChartData(Request $request)
    {
        $validated = $request->validate([
            'year' => 'nullable|integer|min:2000|max:' . now()->year,
        ]);

        return response()->json(
            $this->buildSalesChartPayload($validated['year'] ?? null)
        );
    }

    public function realtimeOrderStats()
    {
        $pendingOrdersCount = Order::where('status', 'pending')->count();
        $allOrdersCount = Order::count();
        $latestOrderId = (int) (Order::max('id') ?? 0);
        $loyaltyEligibleCount = $this->calculateLoyaltyEligibleCount();

        return response()->json([
            'pending_orders_count' => $pendingOrdersCount,
            'all_orders_count' => $allOrdersCount,
            'loyalty_eligible_orders_count' => $loyaltyEligibleCount,
            'latest_order_id' => $latestOrderId,
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    protected function buildSalesChartPayload(?int $year = null): array
    {
        $selectedYear = $year ?? now()->year;

        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        $salesByMonth = Order::query()
            ->selectRaw('MONTH(created_at) as month_number, COUNT(*) as total_sales')
            ->whereYear('created_at', $selectedYear)
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->pluck('total_sales', 'month_number');

        $availableYears = $this->getAvailableSalesYears($selectedYear);

        return [
            'year' => $selectedYear,
            'labels' => array_values($months),
            'data' => collect(array_keys($months))
                ->map(fn ($monthNumber) => (int) $salesByMonth->get($monthNumber, 0))
                ->values()
                ->all(),
            'available_years' => $availableYears,
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    protected function getAvailableSalesYears(?int $selectedYear = null): array
    {
        $currentYear = now()->year;
        $existingYears = Order::query()
            ->selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($year) => (int) $year)
            ->filter()
            ->values();

        $minYear = $existingYears->min() ?? $currentYear;
        $maxYear = max($existingYears->max() ?? $currentYear, min($selectedYear ?? $currentYear, $currentYear));

        return collect(range($minYear, $maxYear))
            ->sortDesc()
            ->values()
            ->all();
    }

    protected function buildSoldItemsReportPayload(?int $year = null, ?int $month = null): array
    {
        $baseQuery = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->whereNotNull('order_items.menu_name');

        $availableYears = Order::query()
            ->where('status', 'completed')
            ->selectRaw('DISTINCT YEAR(created_at) as year')
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($y) => (int) $y)
            ->filter()
            ->values()
            ->all();

        if (empty($availableYears)) {
            $availableYears = [now()->year];
        }

        $selectedYear = in_array((int) $year, $availableYears, true)
            ? (int) $year
            : (int) ($availableYears[0] ?? now()->year);

        $selectedMonth = ($month >= 1 && $month <= 12)
            ? (int) $month
            : null;

        $filteredQuery = (clone $baseQuery)
            ->whereYear('orders.created_at', $selectedYear)
            ->when($selectedMonth, function ($query) use ($selectedMonth) {
                $query->whereMonth('orders.created_at', $selectedMonth);
            });

        $menuSummary = (clone $filteredQuery)
            ->selectRaw('order_items.menu_name as menu_name, SUM(order_items.quantity) as total_quantity, COUNT(DISTINCT orders.id) as total_orders')
            ->groupBy('order_items.menu_name')
            ->orderByDesc('total_quantity')
            ->limit(20)
            ->get();

        $menuHistory = (clone $filteredQuery)
            ->selectRaw('DATE(orders.created_at) as sold_date, order_items.menu_name as menu_name, SUM(order_items.quantity) as total_quantity')
            ->groupByRaw('DATE(orders.created_at), order_items.menu_name')
            ->orderByDesc('sold_date')
            ->orderByDesc('total_quantity')
            ->limit(120)
            ->get();

        $this->attachLocalizedMenuNames($menuSummary);
        $this->attachLocalizedMenuNames($menuHistory);

        $totalItemsSold = (int) ((clone $filteredQuery)->sum('order_items.quantity'));
        $totalMenusSold = (int) $menuSummary->count();

        return [
            'selected_year' => $selectedYear,
            'selected_month' => $selectedMonth,
            'available_years' => $availableYears,
            'available_months' => range(1, 12),
            'total_items_sold' => $totalItemsSold,
            'total_menus_sold' => $totalMenusSold,
            'chart' => [
                'labels' => $menuSummary
                    ->take(10)
                    ->map(fn ($row) => [$row->menu_name_en, $row->menu_name_ar])
                    ->values()
                    ->all(),
                'data' => $menuSummary
                    ->take(10)
                    ->pluck('total_quantity')
                    ->map(fn ($quantity) => (int) $quantity)
                    ->values()
                    ->all(),
            ],
            'summary' => $menuSummary,
            'history' => $menuHistory,
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    protected function attachLocalizedMenuNames($rows): void
    {
        $menuNames = $rows
            ->pluck('menu_name')
            ->filter()
            ->unique()
            ->values();

        if ($menuNames->isEmpty()) {
            return;
        }

        $matchedMenus = Menu::query()
            ->select(['name', 'name_ar'])
            ->where(function ($query) use ($menuNames) {
                $query->whereIn('name', $menuNames)
                    ->orWhereIn('name_ar', $menuNames);
            })
            ->get();

        $rows->transform(function ($row) use ($matchedMenus) {
            $matchedMenu = $matchedMenus->first(function ($menu) use ($row) {
                return $menu->name === $row->menu_name || $menu->name_ar === $row->menu_name;
            });

            $row->menu_name_en = $matchedMenu?->name ?: $row->menu_name;
            $row->menu_name_ar = $matchedMenu?->name_ar ?: $row->menu_name;

            return $row;
        });
    }
    

    public function viewMyProfile()
    {
        $user = Auth::User();  
        return view('admin.view-my-profile', compact('user'));
    }


    public function editMyProfile()
    {
        $user = Auth::User();  
        return view('admin.edit-my-profile', compact('user'));
    }

    public function updateMyProfile(UpdateProfileRequest $request)
    {
        $user = Auth::User();
        $validated = $request->validated();
    
        $user->first_name = $validated['first_name'];
        $user->middle_name = $validated['middle_name']; // Optional, so it can be null
        $user->last_name = $validated['last_name'];        
        $user->email = $validated['email'];
        $user->phone_number = $validated['phone_number'];
        $user->address = $validated['address'];
    
        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old profile photo if exists
            if ($user->profile_picture) {
                Storage::delete('profile-picture/' . $user->profile_picture);
            }
    
            // Store new profile photo
            $photoPath = $request->file('profile_photo')->store('profile-picture', 'public');
            $user->profile_picture = basename($photoPath);
        }
    
        // Save the updated user data
        $user->save();
    
        // Return success message
        return back()->with('success', 'Profile updated successfully.');
    }
    

    public function showChangePasswordForm()
    {
        return view('admin.change-password');
    }

    
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::User();

        // Check if the current password matches the user's password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Send password changed notification email
        Mail::to($user->email)->send(new PasswordChangedNotification($user));

        return redirect()->route('admin.dashboard')->with('success', 'Your password has been successfully updated.');
    }    


    
}
