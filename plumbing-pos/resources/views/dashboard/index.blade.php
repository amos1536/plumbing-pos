@extends('layouts.app')

@section('title', 'Dashboard - Plumbing POS')
@section('page-heading', 'Dashboard')
@section('page-description', 'Overview of your plumbing shop performance')

@section('content')

@php
    $hour = now('Africa/Nairobi')->hour;

    if ($hour < 12) {
        $greeting = 'Good morning';
    } elseif ($hour < 17) {
        $greeting = 'Good afternoon';
    } else {
        $greeting = 'Good evening';
    }
@endphp

<style>
    .pos-dashboard {
        --pos-blue: #2563eb;
        --pos-text: #172033;
        --pos-muted: #64748b;
        --pos-border: #e5eaf2;
        --pos-surface: #ffffff;
        color: var(--pos-text);
    }

    .pos-dashboard .welcome-card,
    .pos-dashboard .stat-card,
    .pos-dashboard .section-card,
    .pos-dashboard .chart-card {
        background: var(--pos-surface);
        border: 1px solid var(--pos-border);
        border-radius: 18px;
        box-shadow: 0 5px 18px rgba(15, 23, 42, .045);
    }

    .pos-dashboard .welcome-card {
        padding: 26px;
        margin-bottom: 24px;
        background: linear-gradient(125deg, #eff6ff, #ffffff 70%);
    }

    .pos-dashboard .welcome-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 24px;
    }

    .pos-dashboard .welcome-user {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .pos-dashboard .welcome-user h2 {
        font-size: clamp(22px, 3vw, 30px);
        letter-spacing: -.7px;
        margin: 0 0 8px;
        font-weight: 750;
    }

    .pos-dashboard .welcome-user p {
        margin: 0;
        color: var(--pos-muted);
    }

    .pos-dashboard .welcome-icon {
        width: 76px;
        height: 76px;
        border-radius: 22px;
        background: #dbeafe;
        color: #1d4ed8;
    }

    .pos-dashboard .welcome-bottom {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 28px;
        margin-top: 26px;
        padding-top: 22px;
        border-top: 1px solid #dce7f5;
    }

    .pos-dashboard .date-time-item {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .pos-dashboard .date-time-label {
        color: var(--pos-muted);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .8px;
        margin-bottom: 5px;
    }

    .pos-dashboard .date-time-value {
        font-size: 15px;
        font-weight: 700;
    }

    .pos-dashboard .clock-value {
        font-size: 22px;
        font-variant-numeric: tabular-nums;
    }

    .pos-dashboard .icon-box {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 76px;
        height: 76px;
        border-radius: 20px;
    }

    .pos-dashboard .icon-box svg {
        display: block;
        width: 42px;
        height: 42px;
        stroke: currentColor;
        fill: none;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .pos-dashboard .icon-blue {
        color: #1d4ed8;
        background: #dbeafe;
    }

    .pos-dashboard .icon-cyan {
        color: #0369a1;
        background: #e0f2fe;
    }

    .pos-dashboard .icon-orange {
        color: #c2410c;
        background: #ffedd5;
    }

    .pos-dashboard .icon-green {
        color: #047857;
        background: #d1fae5;
    }

    .pos-dashboard .icon-purple {
        color: #7e22ce;
        background: #f3e8ff;
    }

    .pos-dashboard .icon-red {
        color: #b91c1c;
        background: #fee2e2;
    }

    .pos-dashboard .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
    }

    .pos-dashboard .stat-card {
        padding: 23px;
        min-width: 0;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .pos-dashboard .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(15, 23, 42, .09);
    }

    .pos-dashboard .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .pos-dashboard .stat-label {
        color: var(--pos-muted);
        font-size: 14px;
        font-weight: 600;
        line-height: 1.5;
    }

    .pos-dashboard .stat-value {
        font-size: clamp(20px, 2vw, 27px);
        line-height: 1.4;
        letter-spacing: -.6px;
        font-weight: 750;
        margin: 20px 0 8px;
        overflow-wrap: anywhere;
    }

    .pos-dashboard .stat-note {
        color: var(--pos-muted);
        font-size: 13px;
        margin: 0;
        line-height: 1.5;
    }

    .pos-dashboard .section-card {
        padding: 25px;
        margin-top: 24px;
        overflow: hidden;
    }

    .pos-dashboard .section-heading {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 22px;
    }

    .pos-dashboard .section-heading .icon-box {
        width: 58px;
        height: 58px;
        border-radius: 16px;
    }

    .pos-dashboard .section-heading .icon-box svg {
        width: 32px;
        height: 32px;
    }

    .pos-dashboard .section-heading h2 {
        font-size: 20px;
        font-weight: 750;
        margin: 0 0 4px;
    }

    .pos-dashboard .section-heading p {
        margin: 0;
        color: var(--pos-muted);
        font-size: 13px;
    }

    /* Analytics charts */
    .pos-dashboard .charts-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        margin-top: 24px;
    }

    .pos-dashboard .chart-card {
        padding: 24px;
        min-width: 0;
        overflow: hidden;
    }

    .pos-dashboard .chart-card.chart-wide {
        grid-column: 1 / -1;
    }

    .pos-dashboard .chart-heading {
        display: flex;
        align-items: center;
        gap: 13px;
        margin-bottom: 8px;
    }

    .pos-dashboard .chart-heading .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
    }

    .pos-dashboard .chart-heading .icon-box svg {
        width: 27px;
        height: 27px;
    }

    .pos-dashboard .chart-heading h2 {
        margin: 0 0 4px;
        font-size: 18px;
        font-weight: 750;
    }

    .pos-dashboard .chart-heading p {
        margin: 0;
        color: var(--pos-muted);
        font-size: 13px;
    }

    .pos-dashboard .chart-container {
        position: relative;
        width: 100%;
        height: 300px;
        margin-top: 20px;
    }

    .pos-dashboard .chart-wide .chart-container {
        height: 340px;
    }

    .pos-dashboard .chart-summary {
        margin-top: 12px;
        padding-top: 14px;
        border-top: 1px solid var(--pos-border);
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 10px;
        color: var(--pos-muted);
        font-size: 13px;
    }

    .pos-dashboard .chart-summary strong {
        color: var(--pos-text);
    }

    .pos-dashboard .product-rank {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 9px;
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 750;
    }

    .pos-dashboard .table-responsive {
        overflow-x: auto;
    }

    .pos-dashboard table {
        width: 100%;
        border-collapse: collapse;
    }

    .pos-dashboard th {
        color: var(--pos-muted);
        background: #f8fafc;
        text-align: left;
        padding: 14px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .5px;
        white-space: nowrap;
    }

    .pos-dashboard td {
        padding: 15px 14px;
        border-bottom: 1px solid #edf0f5;
        font-size: 14px;
    }

    .pos-dashboard tbody tr:last-child td {
        border-bottom: 0;
    }

    .pos-dashboard tbody tr:hover {
        background: #f8fafc;
    }

    .pos-dashboard .section-action {
        margin-top: 20px;
    }

    .pos-dashboard .empty-state {
        padding: 20px 0;
        color: var(--pos-muted);
    }

    .pos-dashboard .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 999px;
        background: #dcfce7;
        color: #166534;
        font-size: 12px;
        font-weight: 700;
        text-transform: capitalize;
    }

    @media (max-width: 1000px) {
        .pos-dashboard .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .pos-dashboard .charts-grid {
            grid-template-columns: 1fr;
        }

        .pos-dashboard .chart-card.chart-wide {
            grid-column: auto;
        }
    }

    @media (max-width: 600px) {
        .pos-dashboard .welcome-card,
        .pos-dashboard .section-card,
        .pos-dashboard .chart-card {
            padding: 18px;
        }

        .pos-dashboard .welcome-icon {
            width: 60px;
            height: 60px;
        }

        .pos-dashboard .welcome-bottom {
            gap: 20px;
        }

        .pos-dashboard .stats-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .pos-dashboard .stat-card {
            padding: 19px;
        }

        .pos-dashboard .icon-box {
            width: 64px;
            height: 64px;
            border-radius: 17px;
        }

        .pos-dashboard .icon-box svg {
            width: 36px;
            height: 36px;
        }

        .pos-dashboard .chart-container,
        .pos-dashboard .chart-wide .chart-container {
            height: 260px;
        }
    }
</style>

<div class="pos-dashboard">

    {{-- Welcome panel --}}
    <div class="welcome-card">
        <div class="welcome-top">
            <div class="welcome-user">
                <span class="icon-box welcome-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M3 21h18"/>
                        <path d="M5 21V7l8-4v18"/>
                        <path d="M13 10h6v11"/>
                        <path d="M8 9v1M8 13v1M8 17v1M16 14v1M16 18v1"/>
                    </svg>
                </span>

                <div>
                    <h2>{{ $greeting }}, {{ auth()->user()->name }}!</h2>
                    <p>Welcome back to your Plumbing Shop POS dashboard.</p>
                </div>
            </div>
        </div>

        <div class="welcome-bottom">
            <div class="date-time-item">
                <span class="icon-box icon-blue">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M16 3v4M8 3v4M3 11h18"/>
                    </svg>
                </span>

                <div>
                    <div class="date-time-label">Today's Date</div>
                    <div class="date-time-value" id="dashboard-date">
                        Loading date...
                    </div>
                </div>
            </div>

            <div class="date-time-item">
                <span class="icon-box icon-green">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 7v5l3 2"/>
                    </svg>
                </span>

                <div>
                    <div class="date-time-label">Current Time · EAT</div>
                    <div class="date-time-value clock-value" id="dashboard-time">
                        Loading time...
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics cards --}}
    <div class="stats-grid">

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Today's Sales</span>
                <span class="icon-box icon-blue">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="9" cy="21" r="1"/>
                        <circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.7 13.4a2 2 0 002 1.6h9.7a2 2 0 002-1.6L23 6H6"/>
                    </svg>
                </span>
            </div>
            <div class="stat-value">KSh {{ number_format((float) $todayRevenue, 2) }}</div>
            <p class="stat-note">Completed sales today</p>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Transactions Today</span>
                <span class="icon-box icon-cyan">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 2h9l5 5v15H6z"/>
                        <path d="M14 2v6h6M9 13h8M9 17h8"/>
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $todayTransactions }}</div>
            <p class="stat-note">Completed transactions</p>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Today's Expenses</span>
                <span class="icon-box icon-orange">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="2" y="5" width="20" height="15" rx="2"/>
                        <path d="M2 10h20M16 15h2"/>
                    </svg>
                </span>
            </div>
            <div class="stat-value">KSh {{ number_format((float) $todayExpenses, 2) }}</div>
            <p class="stat-note">Recorded expenses today</p>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Estimated Gross Profit</span>
                <span class="icon-box icon-green">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M3 3v18h18"/>
                        <path d="M7 14l5-5 4 4 5-7"/>
                        <path d="M16 6h5v5"/>
                    </svg>
                </span>
            </div>
            <div class="stat-value">KSh {{ number_format((float) $todayGrossProfit, 2) }}</div>
            <p class="stat-note">Before expenses</p>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Estimated Net Profit Today</span>
                <span class="icon-box icon-purple">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path d="M3 10h18M7 15h4M16 14v3M14.5 15.5h3"/>
                    </svg>
                </span>
            </div>
            <div class="stat-value">KSh {{ number_format((float) $todayNetProfit, 2) }}</div>
            <p class="stat-note">Gross profit minus recorded expenses</p>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Low-Stock Products</span>
                <span class="icon-box icon-red">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M21 8a2 2 0 00-1-1.7l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.7l7 4a2 2 0 002 0l7-4a2 2 0 001-1.7z"/>
                        <path d="M3.3 7l8.7 5 8.7-5M12 22V12"/>
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $lowStockCount }}</div>
            <p class="stat-note">Products needing attention</p>
        </div>

    </div>

    {{-- Sales analytics --}}
    <div class="charts-grid">

        {{-- Daily sales line chart --}}
        <div class="chart-card">
            <div class="chart-heading">
                <span class="icon-box icon-blue">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M3 3v18h18"/>
                        <path d="M7 14l5-5 4 4 5-7"/>
                    </svg>
                </span>
                <div>
                    <h2>Total Sales Over Time</h2>
                    <p>Daily revenue for the last 30 days</p>
                </div>
            </div>

            <div class="chart-container">
                <canvas id="salesOverTimeChart"
                        aria-label="Daily sales revenue for the last 30 days"
                        role="img"></canvas>
            </div>

            <div class="chart-summary">
                <span>Period: <strong>Last 30 days</strong></span>
                <span>Currency: <strong>KSh</strong></span>
            </div>
        </div>

        {{-- Monthly revenue bar chart --}}
        <div class="chart-card">
            <div class="chart-heading">
                <span class="icon-box icon-green">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M3 3v18h18"/>
                        <path d="M7 16v-5M12 16V6M17 16v-8M22 16V3"/>
                    </svg>
                </span>
                <div>
                    <h2>Monthly Revenue</h2>
                    <p>Revenue comparison for the last 12 months</p>
                </div>
            </div>

            <div class="chart-container">
                <canvas id="monthlyRevenueChart"
                        aria-label="Monthly revenue for the last 12 months"
                        role="img"></canvas>
            </div>

            <div class="chart-summary">
                <span>Period: <strong>Last 12 months</strong></span>
                <span>Currency: <strong>KSh</strong></span>
            </div>
        </div>

        {{-- Top five products chart --}}
        <div class="chart-card chart-wide">
            <div class="chart-heading">
                <span class="icon-box icon-purple">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3l2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3l-5.6 2.9 1.1-6.2L3 9.6l6.2-.9L12 3z"/>
                    </svg>
                </span>
                <div>
                    <h2>Top 5 Performing Products</h2>
                    <p>Products ranked by total quantity sold</p>
                </div>
            </div>

            <div class="chart-container">
                <canvas id="topProductsChart"
                        aria-label="Top five products ranked by quantity sold"
                        role="img"></canvas>
            </div>

            <div class="chart-summary">
                <span>Ranking: <strong>Units sold</strong></span>
                <span>Revenue is displayed in the chart tooltip.</span>
            </div>

            {{-- Detailed top-products table --}}
            @if(isset($topProducts) && $topProducts->isNotEmpty())
                <div class="table-responsive" style="margin-top: 24px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Product</th>
                                <th>Units Sold</th>
                                <th>Sales Revenue (KSh)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $index => $product)
                                <tr>
                                    <td>
                                        <span class="product-rank">{{ $index + 1 }}</span>
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ number_format((float) $product->units_sold, 3) }}</td>
                                    <td>{{ number_format((float) $product->total_revenue, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="empty-state">No completed product sales are available for ranking yet.</p>
            @endif
        </div>

    </div>

    {{-- Low-stock products --}}
    <div class="section-card">
        <div class="section-heading">
            <span class="icon-box icon-orange">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M21 8a2 2 0 00-1-1.7l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.7l7 4a2 2 0 002 0l7-4a2 2 0 001-1.7z"/>
                    <path d="M3.3 7l8.7 5 8.7-5M12 22V12"/>
                </svg>
            </span>
            <div>
                <h2>Low-Stock Products</h2>
                <p>Products that may need replenishment</p>
            </div>
        </div>

        @if($lowStockProducts->isEmpty())
            <p class="empty-state">No low-stock products found.</p>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Quantity Available</th>
                            <th>Reorder Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ number_format((float) $product->quantity_on_hand, 3) }}</td>
                                <td>{{ number_format((float) $product->reorder_level, 3) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="section-action">
            <a href="{{ route('stock.index') }}" class="btn-secondary">
                View Stock Inventory
            </a>
        </div>
    </div>

    {{-- Recent sales --}}
    <div class="section-card">
        <div class="section-heading">
            <span class="icon-box icon-blue">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1z"/>
                    <path d="M8 8h8M8 12h8M8 16h5"/>
                </svg>
            </span>
            <div>
                <h2>Recent Sales</h2>
                <p>Latest transactions recorded in the system</p>
            </div>
        </div>

        @if($recentSales->isEmpty())
            <p class="empty-state">No sales records found.</p>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Receipt Number</th>
                            <th>Date</th>
                            <th>Cashier</th>
                            <th>Total (KSh)</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSales as $sale)
                            <tr>
                                <td>{{ $sale->receipt_number }}</td>
                                <td>
                                    {{ $sale->sold_at
                                        ? $sale->sold_at->format('d M Y, H:i')
                                        : $sale->created_at->format('d M Y, H:i') }}
                                </td>
                                <td>{{ $sale->user?->name ?? 'Unknown' }}</td>
                                <td>{{ number_format((float) $sale->total_amount, 2) }}</td>
                                <td>
                                    <span class="status-badge">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('sales.receipt', $sale) }}"
                                       class="btn-secondary">
                                        View Receipt
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="section-action">
            <a href="{{ route('sales.index') }}" class="btn-secondary">
                View All Sales
            </a>
        </div>
    </div>

</div>

@endsection

{{-- Load Chart.js and initialize dashboard charts through the main layout's script stack --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /*
     * Chart data is provided by DashboardController.
     * The fallback arrays prevent JavaScript errors if data is empty.
     */
    const salesLabels = @json($salesOverTimeLabels ?? []);
    const salesData = @json($salesOverTimeData ?? []);

    const monthlyLabels = @json($monthlyRevenueLabels ?? []);
    const monthlyData = @json($monthlyRevenueData ?? []);

    const productLabels = @json($topProductsLabels ?? []);
    const productQuantity = @json($topProductsQuantity ?? []);
    const productRevenue = @json($topProductsRevenue ?? []);

    const currency = function (value) {
        return 'KSh ' + Number(value || 0).toLocaleString('en-KE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    Chart.defaults.font.family =
        'Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';

    Chart.defaults.color = '#64748b';

    // 1. Daily sales revenue line chart
    const salesCanvas = document.getElementById('salesOverTimeChart');

    if (salesCanvas) {
        new Chart(salesCanvas, {
            type: 'line',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Sales Revenue',
                    data: salesData,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.12)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#2563eb'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'Revenue: ' + currency(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxTicksLimit: 8,
                            maxRotation: 0
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'KSh ' + Number(value).toLocaleString('en-KE');
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Monthly revenue bar chart
    const monthlyCanvas = document.getElementById('monthlyRevenueChart');

    if (monthlyCanvas) {
        new Chart(monthlyCanvas, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Monthly Revenue',
                    data: monthlyData,
                    backgroundColor: '#10b981',
                    hoverBackgroundColor: '#059669',
                    borderRadius: 7,
                    maxBarThickness: 42
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'Revenue: ' + currency(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'KSh ' + Number(value).toLocaleString('en-KE');
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. Top five products, ranked by quantity sold.
    // Hovering over a bar also displays the product's revenue.
    const productsCanvas = document.getElementById('topProductsChart');

    if (productsCanvas) {
        new Chart(productsCanvas, {
            type: 'bar',
            data: {
                labels: productLabels,
                datasets: [{
                    label: 'Units Sold',
                    data: productQuantity,
                    backgroundColor: '#8b5cf6',
                    hoverBackgroundColor: '#7c3aed',
                    borderRadius: 7,
                    maxBarThickness: 38
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const index = context.dataIndex;
                                const quantity = Number(context.parsed.x || 0);
                                const revenue = Number(productRevenue[index] || 0);

                                return [
                                    'Units sold: ' + quantity.toLocaleString('en-KE'),
                                    'Revenue: ' + currency(revenue)
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        title: {
                            display: true,
                            text: 'Quantity sold'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Live date and time in Kenya
    function updateDashboardDateTime() {
        const currentTime = new Date();

        const dateOptions = {
            timeZone: 'Africa/Nairobi',
            weekday: 'long',
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        };

        const timeOptions = {
            timeZone: 'Africa/Nairobi',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        };

        const dateElement = document.getElementById('dashboard-date');
        const timeElement = document.getElementById('dashboard-time');

        if (dateElement) {
            dateElement.textContent =
                new Intl.DateTimeFormat('en-KE', dateOptions).format(currentTime);
        }

        if (timeElement) {
            timeElement.textContent =
                new Intl.DateTimeFormat('en-KE', timeOptions).format(currentTime);
        }
    }

    updateDashboardDateTime();
    setInterval(updateDashboardDateTime, 1000);
});
</script>
@endpush
