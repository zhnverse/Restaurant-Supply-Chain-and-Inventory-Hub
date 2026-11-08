<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Restaurant Supply Hub</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #3b82f6;
            --sidebar-width: 270px;
            --topbar-height: 64px;
            --accent: #3b82f6;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            margin: 0;
            overflow-x: hidden;
            color: #1e293b;
        }

        /* ───── SIDEBAR ───── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-brand .brand-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; color: white; margin-bottom: 0.5rem;
        }

        .sidebar-brand h6 {
            color: #fff;
            font-size: 0.9rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }

        .sidebar-brand small {
            color: #64748b;
            font-size: 0.72rem;
        }

        .sidebar-nav {
            flex: 1;
            padding: 0.75rem 0;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 0.65rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.85rem 1.25rem 0.35rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1.25rem;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.15s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-link:hover {
            background: var(--sidebar-hover);
            color: #e2e8f0;
            text-decoration: none;
        }

        .sidebar-link.active {
            background: rgba(59,130,246,0.15);
            color: #60a5fa;
            border-left-color: var(--sidebar-active);
        }

        .sidebar-link .nav-icon {
            width: 20px;
            text-align: center;
            font-size: 0.95rem;
            opacity: 0.85;
        }

        .sidebar-link .badge-pill {
            margin-left: auto;
            font-size: 0.65rem;
            padding: 2px 7px;
            border-radius: 20px;
        }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,0.08);
            background: rgba(0,0,0,0.2);
        }

        .sidebar-footer .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 0.8rem; font-weight: 700;
        }

        /* ───── MAIN CONTENT ───── */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ───── TOPBAR ───── */
        .topbar {
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.75rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .page-content {
            flex: 1;
            padding: 1.75rem;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-size: 1.55rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .page-header p {
            color: #64748b;
            margin: 0.25rem 0 0;
            font-size: 0.875rem;
        }

        /* ───── CARDS & BADGES ───── */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07), 0 4px 16px rgba(0,0,0,0.03);
            background: #fff;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 1rem 1.25rem;
            font-weight: 600;
            font-size: 0.92rem;
            color: #1e293b;
        }

        .card-body { padding: 1.25rem; }

        .stat-card {
            border-radius: 12px;
            padding: 1.25rem;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: -20px; right: -20px;
            width: 90px; height: 90px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
        }

        .stat-card .stat-icon {
            width: 42px; height: 42px;
            border-radius: 10px;
            background: rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 0.75rem;
        }

        .stat-card .stat-number {
            font-size: 1.85rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }

        .stat-card .stat-label {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.85);
            margin-top: 0.3rem;
            font-weight: 500;
        }

        .stat-blue   { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .stat-green  { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-red    { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .stat-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .stat-teal   { background: linear-gradient(135deg, #06b6d4, #0891b2); }

        .badge-status {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.35em 0.75em;
            border-radius: 6px;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .badge-pending, .badge-draft { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }

        .filter-tabs { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
        .filter-tab {
            padding: 0.45rem 1rem;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s;
            border: 1px solid #e2e8f0;
            color: #64748b;
            background: #fff;
        }
        .filter-tab:hover { background: #f1f5f9; color: #334155; text-decoration: none; }
        .filter-tab.active { background: #3b82f6; color: #fff; border-color: #3b82f6; }

        .diff-surplus  { color: #059669; font-weight: 700; }
        .diff-shortage { color: #dc2626; font-weight: 700; }
        .diff-balanced { color: #94a3b8; }

        .timeline { position: relative; padding-left: 1.75rem; }
        .timeline::before {
            content: '';
            position: absolute;
            left: 0.55rem; top: 0; bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }
        .timeline-item { position: relative; padding-bottom: 1.25rem; }
        .timeline-dot {
            position: absolute;
            left: -1.5rem; top: 0.2rem;
            width: 13px; height: 13px;
            border-radius: 50%;
            border: 2px solid #fff;
        }
        .dot-blue   { background: #3b82f6; box-shadow: 0 0 0 2px #bfdbfe; }
        .dot-green  { background: #10b981; box-shadow: 0 0 0 2px #a7f3d0; }
        .dot-red    { background: #ef4444; box-shadow: 0 0 0 2px #fecaca; }
        .dot-gray   { background: #94a3b8; box-shadow: 0 0 0 2px #e2e8f0; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

@php
    $currentUser = auth()->user() ?? \App\Models\User::first();
    $pendingRecon = \App\Models\StockReconciliation::where('status','pending')->count();
    $pendingWaste = \App\Models\WasteReport::where('status','pending')->count();
    $draftPOs     = \App\Models\PurchaseOrder::where('status','draft')->count();
    $expiringBatches = \App\Models\IngredientBatch::whereNotNull('expiry_date')
        ->where('expiry_date', '>=', now()->toDateString())
        ->where('expiry_date', '<=', now()->addDays(7)->toDateString())
        ->count();
@endphp

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fa-solid fa-utensils"></i></div>
        <h6>Restaurant Supply Hub</h6>
        <small>Inventory &amp; Supply Chain Hub</small>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section-label">Overview</div>
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge nav-icon"></i> Dashboard
        </a>

        <div class="nav-section-label">Kitchen &amp; Sales (F1)</div>
        <a href="{{ route('sales.create') }}" class="sidebar-link {{ request()->routeIs('sales.create') ? 'active' : '' }}">
            <i class="fa-solid fa-cash-register nav-icon text-success"></i> Record Dish Sale (POS)
        </a>
        <a href="{{ route('sales.index') }}" class="sidebar-link {{ request()->routeIs('sales.index') ? 'active' : '' }}">
            <i class="fa-solid fa-receipt nav-icon"></i> Sales Deduction History
        </a>
        <a href="{{ route('dishes.index') }}" class="sidebar-link {{ request()->routeIs('dishes.*') ? 'active' : '' }}">
            <i class="fa-solid fa-bowl-food nav-icon"></i> Dishes &amp; Recipes
        </a>

        <div class="nav-section-label">Inventory &amp; Batches (F2)</div>
        <a href="{{ route('batches.index') }}" class="sidebar-link {{ request()->routeIs('batches.*') ? 'active' : '' }}">
            <i class="fa-solid fa-clock-rotate-left nav-icon text-warning"></i> Expiry Monitoring
            @if($expiringBatches > 0)
                <span class="badge-pill bg-warning text-dark badge-status">{{ $expiringBatches }}</span>
            @endif
        </a>

        <div class="nav-section-label">Procurement &amp; Orders (F3 &amp; F4)</div>
        <a href="{{ route('purchase-orders.index') }}" class="sidebar-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
            <i class="fa-solid fa-cart-shopping nav-icon text-info"></i> Purchase Orders
            @if($draftPOs > 0)
                <span class="badge-pill bg-info text-dark badge-status">{{ $draftPOs }} draft</span>
            @endif
        </a>

        <div class="nav-section-label">Stock Control (F5 &amp; F6)</div>
        <a href="{{ route('stock-reconciliations.index') }}" class="sidebar-link {{ request()->routeIs('stock-reconciliations.*') ? 'active' : '' }}">
            <i class="fa-solid fa-scale-balanced nav-icon text-primary"></i> Stock Reconciliation
            @if($pendingRecon > 0)
                <span class="badge-pill bg-warning text-dark badge-status">{{ $pendingRecon }}</span>
            @endif
        </a>

        <a href="{{ route('waste-reports.index') }}" class="sidebar-link {{ request()->routeIs('waste-reports.*') ? 'active' : '' }}">
            <i class="fa-solid fa-trash-can nav-icon text-danger"></i> Waste Reports
            @if($pendingWaste > 0)
                <span class="badge-pill bg-danger text-white badge-status">{{ $pendingWaste }}</span>
            @endif
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="user-avatar">{{ strtoupper(substr($currentUser->name ?? 'U', 0, 1)) }}</div>
                <div>
                    <div style="color:#e2e8f0; font-size:0.8rem; font-weight:600;">{{ $currentUser->name ?? 'User' }}</div>
                    <div style="color:#94a3b8; font-size:0.7rem;" class="text-capitalize">
                        <span class="badge bg-{{ ($currentUser->role ?? 'staff') === 'manager' ? 'primary' : 'secondary' }} py-0">
                            {{ $currentUser->role ?? 'staff' }}
                        </span>
                    </div>
                </div>
            </div>
            @if(auth()->check())
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light py-1 px-2" title="Sign Out">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light py-1 px-2" title="Sign In">
                    <i class="fa-solid fa-right-to-bracket"></i>
                </a>
            @endif
        </div>
    </div>
</nav>

<!-- Main Wrapper -->
<div class="main-wrapper">

    <!-- Topbar -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-md-none" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted text-decoration-none">Home</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="topbar-actions d-flex align-items-center gap-2">
            <!-- Quick POS sale button -->
            <a href="{{ route('sales.create') }}" class="btn btn-sm btn-success">
                <i class="fa-solid fa-cash-register me-1"></i> POS Sale
            </a>

            <!-- Quick Auto-reorder check -->
            <form method="POST" action="{{ route('purchase-orders.auto-draft') }}" class="d-inline m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-info" title="Run automatic inventory threshold check">
                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Auto-Reorder Check
                </button>
            </form>

            <a href="{{ route('waste-reports.create') }}" class="btn btn-sm btn-danger">
                <i class="fa-solid fa-trash-can me-1"></i> Waste
            </a>

            @if(auth()->check())
                <div class="dropdown ms-2">
                    <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-user-circle me-1"></i> Switch Role
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><a class="dropdown-item small" href="{{ route('login.quick', 'manager') }}"><i class="fa-solid fa-user-tie me-2 text-primary"></i>Manager (Sarah Ahmed)</a></li>
                        <li><a class="dropdown-item small" href="{{ route('login.quick', 'staff') }}"><i class="fa-solid fa-user-pen me-2 text-secondary"></i>Staff (Rahim Mia)</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item small text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i>Sign Out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary ms-2">Sign In</a>
            @endif
        </div>
    </header>

    <!-- Page Content -->
    <main class="page-content">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="fa-solid fa-circle-check"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>{{ session('warning') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="fa-solid fa-circle-info"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fa-solid fa-circle-xmark me-1"></i>
                <strong>Action Alert:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}
</script>
@stack('scripts')
</body>
</html>
