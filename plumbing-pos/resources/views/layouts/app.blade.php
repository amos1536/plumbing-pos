
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Plumbing Shop POS')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Page-specific styles --}}
    @stack('styles')
</head>
<body>

<div class="app-layout">

    <aside class="sidebar">

        {{-- Brand --}}
        <div class="brand">
            <div class="brand-logo">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M7 3v6a5 5 0 0 0 10 0V3M7 6H4v3a8 8 0 0 0 16 0V6h-3M12 14v7"/>
                </svg>
            </div>

            <div>
                <h2>Plumbing POS</h2>
                <p>Shop Management</p>
            </div>
        </div>

        <nav class="navigation" aria-label="Main navigation">

            <span class="nav-section-title">MAIN MENU</span>

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
                <span>Dashboard</span>
            </a>

            @if(auth()->user()?->role === 'admin')

                <span class="nav-section-title">INVENTORY</span>

                {{-- Products --}}
                <a href="{{ route('products.index') }}"
                   class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m12 3 9 5-9 5-9-5 9-5Z"/>
                        <path d="m3 12 9 5 9-5M3 16l9 5 9-5"/>
                    </svg>
                    <span>Products</span>
                </a>

                {{-- Categories --}}
                <a href="{{ route('categories.index') }}"
                   class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M3 6a2 2 0 0 1 2-2h5l2 3h7a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6Z"/>
                    </svg>
                    <span>Categories</span>
                </a>

                {{-- Suppliers --}}
                <a href="{{ route('suppliers.index') }}"
                   class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M3 6h11v12H3zM14 10h4l3 4v4h-7z"/>
                        <circle cx="7.5" cy="18" r="2"/>
                        <circle cx="17.5" cy="18" r="2"/>
                    </svg>
                    <span>Suppliers</span>
                </a>

                {{-- Stock Inventory --}}
                <a href="{{ route('stock.index') }}"
                   class="nav-link {{ request()->routeIs('stock.index') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M3 21V8l9-5 9 5v13H3Z"/>
                        <path d="M3 8h18M8 21v-8h8v8M8 8v4m8-4v4"/>
                    </svg>
                    <span>Stock Inventory</span>
                </a>

                {{-- Receive Stock --}}
                <a href="{{ route('stock.receive.create') }}"
                   class="nav-link {{ request()->routeIs('stock.receive.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3v12m-5-5 5 5 5-5"/>
                        <path d="M5 16v4h14v-4"/>
                    </svg>
                    <span>Receive Stock</span>
                </a>

                {{-- Stock Movements --}}
                <a href="{{ route('stock-movements.index') }}"
                   class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M7 7h13l-3-3m3 3-3 3M17 17H4l3 3m-3-3 3-3"/>
                    </svg>
                    <span>Stock Movements</span>
                </a>

                <span class="nav-section-title">MANAGEMENT</span>

                {{-- User Management --}}
                <a href="{{ route('users.index') }}"
                   class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="9" cy="8" r="4"/>
                        <path d="M2 21v-2a7 7 0 0 1 14 0v2M16 4a4 4 0 0 1 0 8m2 3a6 6 0 0 1 4 6"/>
                    </svg>
                    <span>User Management</span>
                </a>

                {{-- Sales Reports --}}
                <a href="{{ route('reports.sales') }}"
                   class="nav-link {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 20V10m8 10V4m8 16v-7"/>
                        <path d="M2 20h20"/>
                    </svg>
                    <span>Sales Reports</span>
                </a>

                {{-- Expenses --}}
                <a href="{{ route('expenses.index') }}"
                   class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="15" rx="2"/>
                        <path d="M16 14h5M3 9h18M7 5V3h10v2"/>
                        <circle cx="16" cy="14" r="1"/>
                    </svg>
                    <span>Expenses</span>
                </a>

                {{-- Audit Logs --}}
                <a href="{{ route('audit-logs.index') }}"
                   class="nav-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M7 3h10l3 3v15H4V3h3Z"/>
                        <path d="M8 11h8M8 15h8M8 7h4"/>
                        <path d="m8 19 2 2 5-5"/>
                    </svg>
                    <span>Audit Logs</span>
                </a>

            @endif

            <span class="nav-section-title">TRANSACTIONS</span>

            {{-- Sales --}}
            <a href="{{ route('sales.index') }}"
               class="nav-link {{ request()->routeIs('sales.index', 'sales.create') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="19" cy="21" r="1"/>
                    <path d="M2 3h2l3 13h12l3-9H5"/>
                </svg>
                <span>Sales</span>
            </a>

            {{-- Sales Returns --}}
            <a href="{{ route('sales-returns.index') }}"
               class="nav-link {{ request()->routeIs('sales-returns.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M9 14 4 9l5-5"/>
                    <path d="M4 9h10a6 6 0 0 1 0 12h-2"/>
                </svg>
                <span>Sales Returns</span>
            </a>

            {{-- Draft Sales --}}
            <a href="{{ route('sales.drafts') }}"
               class="nav-link {{ request()->routeIs('sales.drafts') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="5" y="3" width="14" height="18" rx="2"/>
                    <path d="M9 3V2h6v1"/>
                    <path d="M8 9h8M8 13h8M8 17h5"/>
                </svg>
                <span>Draft Sales</span>
            </a>

        </nav>

        {{-- Logged-in user --}}
        <div class="user-info">
            <div class="user-avatar">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 21v-2a8 8 0 0 1 16 0v2"/>
                </svg>
            </div>

            <div class="user-details">
                <p>{{ auth()->user()?->name }}</p>
                <small>{{ ucfirst(auth()->user()?->role ?? '') }}</small>
            </div>
        </div>

        {{-- Logout --}}
        <form action="{{ route('logout') }}"
              method="POST"
              class="logout-form">
            @csrf

            <button type="submit" class="logout-button">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M10 17l5-5-5-5m5 5H3"/>
                    <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/>
                </svg>
                <span>Log Out</span>
            </button>
        </form>

    </aside>

    {{-- Main Content --}}
    <main class="main-content">

        <header class="topbar">
            <div>
                <h1>@yield('page-heading', 'Shop Management')</h1>
                <p>@yield('page-description', 'Manage your plumbing shop')</p>
            </div>
        </header>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success" role="status">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Message --}}
        @if(session('error'))
            <div class="alert alert-error" role="alert">
                {{ session('error') }}
            </div>
        @endif

        {{-- Page Content --}}
        <section class="page-content">
            @yield('content')
        </section>

    </main>

</div>

{{-- Page-specific JavaScript --}}
@stack('scripts')

</body>
</html>
