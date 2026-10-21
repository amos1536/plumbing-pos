<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        // Validate report dates
        $validated = $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:start_date',
            ],
        ]);

        // Default to today's date
        $startDate = $validated['start_date'] ?? now()->toDateString();
        $endDate = $validated['end_date'] ?? now()->toDateString();

        // Completed sales within the selected period
        $salesQuery = Sale::query()
            ->where('status', 'completed')
            ->whereNotNull('sold_at')
            ->whereBetween('sold_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59',
            ]);

        // Total revenue
        $totalRevenue = (clone $salesQuery)->sum('total_amount');

        // Number of completed transactions
        $totalTransactions = (clone $salesQuery)->count();

        // Cost of goods sold
        $totalCost = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereNotNull('sales.sold_at')
            ->whereBetween('sales.sold_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59',
            ])
            ->selectRaw(
                'COALESCE(SUM(sale_items.quantity * sale_items.cost_price), 0) AS total_cost'
            )
            ->value('total_cost');

        // Total recorded expenses for the selected period
        $totalExpenses = DB::table('expenses')
            ->whereBetween('expense_date', [
                $startDate,
                $endDate,
            ])
            ->sum('amount');

        // Estimated gross profit
        $grossProfit = (float) $totalRevenue - (float) $totalCost;

        // Estimated net profit after recorded expenses
        $netProfit = $grossProfit - (float) $totalExpenses;

        // Sales records for the report table
        $sales = (clone $salesQuery)
            ->with('user')
            ->orderByDesc('sold_at')
            ->paginate(15)
            ->withQueryString();

        return view('reports.sales', compact(
            'startDate',
            'endDate',
            'totalRevenue',
            'totalTransactions',
            'totalCost',
            'totalExpenses',
            'grossProfit',
            'netProfit',
            'sales'
        ));
    }
}

