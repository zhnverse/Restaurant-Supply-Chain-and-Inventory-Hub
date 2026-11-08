<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Restaurant Supply Hub</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #3b82f6;
            --sidebar-width: 260px;
            --topbar-height: 64px;
            --accent: #3b82f6;
            --accent-hover: #2563eb;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            margin: 0;
            overflow-x: hidden;
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
            padding: 1.5rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-brand .brand-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; color: white; margin-bottom: 0.5rem;
        }

        .sidebar-brand h6 {
            color: #fff;
            font-size: 0.875rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.3;
        }

        .sidebar-brand small {
            color: #64748b;
            font-size: 0.72rem;
        }

        .sidebar-nav {
            flex: 1;
            padding: 1rem 0;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 0.65rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.75rem 1.25rem 0.4rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1.25rem;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0;
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
            opacity: 0.8;
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
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .topbar .breadcrumb {
            margin: 0;
            font-size: 0.85rem;
        }

        .topbar .breadcrumb-item + .breadcrumb-item::before {
            content: '/';
            color: #94a3b8;
        }

        .topbar-actions .btn {
            font-size: 0.82rem;
            padding: 0.4rem 0.9rem;
        }

        /* ───── PAGE CONTENT ───── */
        .page-content {
            flex: 1;
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 1.75rem;
        }

        .page-header h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .page-header p {
            color: #64748b;
            margin: 0.25rem 0 0;
            font-size: 0.9rem;
        }

        /* ───── CARDS ───── */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07), 0 4px 16px rgba(0,0,0,0.04);
            background: #fff;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.1rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #1e293b;
        }

        .card-body { padding: 1.5rem; }

        /* ───── STAT CARDS ───── */
        .stat-card {
            border-radius: 12px;
            padding: 1.5rem;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: -20px; right: -20px;
            width: 100px; height: 100px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
        }

        .stat-card .stat-icon {
            width: 48px; height: 48px;
            border-radius: 10px;
            background: rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }

        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }

        .stat-card .stat-label {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.8);
            margin-top: 0.3rem;
            font-weight: 500;
        }

        .stat-blue   { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .stat-green  { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-red    { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .stat-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

        /* ───── BADGES ───── */
        .badge-status {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.35em 0.75em;
            border-radius: 6px;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .badge-pending  { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }

        /* ───── TABLES ───── */
        .table { font-size: 0.875rem; }
        .table thead th {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            background: #f8fafc;
            border-top: none;
            padding: 0.75rem 1rem;
        }
        .table tbody td { padding: 0.9rem 1rem; vertical-align: middle; }
        .table tbody tr:hover { background: #f8fafc; }

        /* ───── FILTER TABS ───── */
        .filter-tabs { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
        .filter-tab {
            padding: 0.5rem 1.1rem;
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

        /* ───── FORMS ───── */
        .form-label { font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 0.4rem; }
        .form-control, .form-select {
            font-size: 0.875rem;
            border-radius: 8px;
            border-color: #d1d5db;
            padding: 0.55rem 0.85rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }

        /* ───── BUTTONS ───── */
        .btn { border-radius: 8px; font-size: 0.875rem; font-weight: 600; }
        .btn-primary { background: #3b82f6; border-color: #3b82f6; }
        .btn-primary:hover { background: #2563eb; border-color: #2563eb; }
        .btn-success { background: #10b981; border-color: #10b981; }
        .btn-success:hover { background: #059669; border-color: #059669; }

        /* ───── ALERTS ───── */
        .alert { border-radius: 10px; border: none; font-size: 0.875rem; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-warning { background: #fef3c7; color: #92400e; }
        .alert-danger  { background: #fee2e2; color: #991b1b; }
        .alert-info    { background: #dbeafe; color: #1e40af; }

        /* ───── DIFF INDICATORS (F5) ───── */
        .diff-surplus  { color: #059669; font-weight: 700; }
        .diff-shortage { color: #dc2626; font-weight: 700; }
        .diff-balanced { color: #94a3b8; }

        /* ───── TIMELINE ───── */
        .timeline { position: relative; padding-left: 2rem; }
        .timeline::before {
            content: '';
            position: absolute;
            left: 0.65rem;
            top: 0; bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }
        .timeline-item { position: relative; padding-bottom: 1.5rem; }
        .timeline-dot {
            position: absolute;
            left: -1.65rem;
            top: 0.2rem;
            width: 14px; height: 14px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #e2e8f0;
        }
        .timeline-dot.dot-blue   { background: #3b82f6; box-shadow: 0 0 0 2px #bfdbfe; }
        .timeline-dot.dot-green  { background: #10b981; box-shadow: 0 0 0 2px #a7f3d0; }
        .timeline-dot.dot-red    { background: #ef4444; box-shadow: 0 0 0 2px #fecaca; }
        .timeline-dot.dot-gray   { background: #94a3b8; box-shadow: 0 0 0 2px #e2e8f0; }

        /* ───── RESPONSIVE ───── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }

        /* ───── TOAST ───── */
        .toast-container { z-index: 9999; }
    </style>
    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fa-solid fa-utensils"></i></div>
        <h6>Restaurant Supply Hub</h6>
        <small>Inventory & Supply Chain</small>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section-label">Overview</div>

        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge nav-icon"></i> Dashboard
        </a>

        <div class="nav-section-label">Inventory Control</div>

        <a href="{{ route('stock-reconciliations.index') }}"
           class="sidebar-link {{ request()->routeIs('stock-reconciliations.*') ? 'active' : '' }}">
            <i class="fa-solid fa-scale-balanced nav-icon"></i>
            Stock Reconciliation
            @php $pendingRecon = \App\Models\StockReconciliation::where('status','pending')->count(); @endphp
            @if($pendingRecon > 0)
                <span class="badge-pill bg-warning text-dark badge-status">{{ $pendingRecon }}</span>
            @endif
        </a>

        <a href="{{ route('waste-reports.index') }}"
           class="sidebar-link {{ request()->routeIs('waste-reports.*') ? 'active' : '' }}">
            <i class="fa-solid fa-trash-can nav-icon"></i>
            Waste Reports
            @php $pendingWaste = \App\Models\WasteReport::where('status','pending')->count(); @endphp
            @if($pendingWaste > 0)
                <span class="badge-pill bg-danger text-white badge-status">{{ $pendingWaste }}</span>
            @endif
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2">
            <div class="user-avatar">M</div>
            <div>
                <div style="color:#e2e8f0; font-size:0.8rem; font-weight:600;">Manager</div>
                <div style="color:#64748b; font-size:0.72rem;">Restaurant #1</div>
            </div>
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
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted text-decoration-none">Home</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="topbar-actions d-flex align-items-center gap-2">
            <a href="{{ route('stock-reconciliations.create') }}" class="btn btn-sm btn-outline-primary">
                <i class="fa-solid fa-plus me-1"></i> New Count
            </a>
            <a href="{{ route('waste-reports.create') }}" class="btn btn-sm btn-primary">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> Report Waste
            </a>
        </div>
    </header>

    <!-- Page Content -->
    <main class="page-content">

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation"></i>
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fa-solid fa-circle-xmark me-1"></i>
                <strong>Please fix the following:</strong>
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

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}
</script>

@stack('scripts')
</body>
</html>
