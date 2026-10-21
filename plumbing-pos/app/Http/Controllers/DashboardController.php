<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $startOfDay = now()->startOfDay();
        $endOfDay = now()->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | Today's Sales
        |--------------------------------------------------------------------------
        */

        $todaySalesQuery = Sale::where('status', 'completed')
            ->whereBetween('sold_at', [
                $startOfDay,
                $endOfDay,
            ]);

        $todayRevenue = (clone $todaySalesQuery)
            ->sum('total_amount');

        $todayTransactions = (clone $todaySalesQuery)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Today's Expenses and Profit
        |--------------------------------------------------------------------------
        */

        $todayExpenses = DB::table('expenses')
            ->whereDate('expense_date', today())
            ->sum('amount');

        $todayCostOfGoods = DB::table('sale_items')
            ->join(
                'sales',
                'sale_items.sale_id',
                '=',
                'sales.id'
            )
            ->where('sales.status', 'completed')
            ->whereBetween('sales.sold_at', [
                $startOfDay,
                $endOfDay,
            ])
            ->selectRaw(
                'COALESCE(
                    SUM(sale_items.quantity * sale_items.cost_price),
                    0
                ) AS total_cost'
            )
            ->value('total_cost');

        $todayGrossProfit = (float) $todayRevenue
            - (float) $todayCostOfGoods;

        $todayNetProfit = $todayGrossProfit
            - (float) $todayExpenses;

        /*
        |--------------------------------------------------------------------------
        | Low Stock Products
        |--------------------------------------------------------------------------
        */

        $lowStockProducts = Product::where('is_active', true)
            ->whereColumn(
                'quantity_on_hand',
                '<=',
                'reorder_level'
            )
            ->orderBy('quantity_on_hand')
            ->limit(10)
            ->get();

        $lowStockCount = Product::where('is_active', true)
            ->whereColumn(
                'quantity_on_hand',
                '<=',
                'reorder_level'
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Sales Against Time - Last 30 Days
        |--------------------------------------------------------------------------
        */

        $dailyStart = now()->subDays(29)->startOfDay();
        $dailyEnd = now()->endOfDay();

        $dailySales = Sale::where('status', 'completed')
            ->whereBetween('sold_at', [
                $dailyStart,
                $dailyEnd,
            ])
            ->selectRaw('DATE(sold_at) AS sale_date')
            ->selectRaw('SUM(total_amount) AS revenue')
            ->groupByRaw('DATE(sold_at)')
            ->orderBy('sale_date')
            ->get()
            ->keyBy('sale_date');

        $salesOverTimeLabels = [];
        $salesOverTimeData = [];

        for ($i = 0; $i < 30; $i++) {
            $date = $dailyStart->copy()->addDays($i);
            $dateKey = $date->toDateString();

            $salesOverTimeLabels[] = $date->format('d M');

            $salesOverTimeData[] = round(
                (float) ($dailySales->get($dateKey)?->revenue ?? 0),
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Monthly Revenue - Last 12 Months
        |--------------------------------------------------------------------------
        */

        $monthlyStart = now()
            ->startOfMonth()
            ->subMonths(11);

        $monthlySales = Sale::where('status', 'completed')
            ->whereBetween('sold_at', [
                $monthlyStart,
                now()->endOfMonth(),
            ])
            ->selectRaw(
                "DATE_FORMAT(sold_at, '%Y-%m') AS sale_month"
            )
            ->selectRaw('SUM(total_amount) AS revenue')
            ->groupByRaw(
                "DATE_FORMAT(sold_at, '%Y-%m')"
            )
            ->orderBy('sale_month')
            ->get()
            ->keyBy('sale_month');

        $monthlyRevenueLabels = [];
        $monthlyRevenueData = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $monthlyStart->copy()->addMonths($i);
            $monthKey = $month->format('Y-m');

            $monthlyRevenueLabels[] = $month->format('M Y');

            $monthlyRevenueData[] = round(
                (float) (
                    $monthlySales->get($monthKey)?->revenue ?? 0
                ),
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Top 5 Performing Products
        |--------------------------------------------------------------------------
        |
        | Ranked by total quantity sold.
        | Revenue uses line_total to account for item discounts.
        |
        */

        $topProducts = DB::table('sale_items')
            ->join(
                'sales',
                'sale_items.sale_id',
                '=',
                'sales.id'
            )
            ->join(
                'products',
                'sale_items.product_id',
                '=',
                'products.id'
            )
            ->where('sales.status', 'completed')
            ->select(
                'products.id',
                'products.name'
            )
            ->selectRaw(
                'SUM(sale_items.quantity) AS units_sold'
            )
            ->selectRaw(
                'SUM(sale_items.line_total) AS total_revenue'
            )
            ->groupBy(
                'products.id',
                'products.name'
            )
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();

        $topProductsLabels = $topProducts
            ->pluck('name')
            ->values();

        $topProductsQuantity = $topProducts
            ->pluck('units_sold')
            ->map(fn ($quantity) => (float) $quantity)
            ->values();

        $topProductsRevenue = $topProducts
            ->pluck('total_revenue')
            ->map(fn ($revenue) => round((float) $revenue, 2))
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Recent Sales
        |--------------------------------------------------------------------------
        */

        $recentSales = Sale::with('user')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Dashboard View
        |--------------------------------------------------------------------------
        */

        return view('dashboard.index', compact(
            'todayRevenue',
            'todayTransactions',
            'todayExpenses',
            'todayCostOfGoods',
            'todayGrossProfit',
            'todayNetProfit',
            'lowStockProducts',
            'lowStockCount',
            'recentSales',
            'salesOverTimeLabels',
            'salesOverTimeData',
            'monthlyRevenueLabels',
            'monthlyRevenueData',
            'topProducts',
            'topProductsLabels',
            'topProductsQuantity',
            'topProductsRevenue'
        ));
    }
}
