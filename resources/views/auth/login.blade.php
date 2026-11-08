<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Restaurant Supply Hub</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #1e293b;
        }

        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .login-header {
            background: #f8fafc;
            padding: 2.2rem 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }

        .brand-badge {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
        }

        .form-control {
            border-radius: 10px;
            padding: 0.65rem 0.95rem;
            font-size: 0.9rem;
            border-color: #cbd5e1;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        .demo-box {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 1rem;
            border: 1px dashed #cbd5e1;
        }

        .btn-demo {
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.4rem 0.8rem;
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="brand-badge"><i class="fa-solid fa-utensils"></i></div>
        <h4 class="fw-bold mb-1">Restaurant Supply Hub</h4>
        <p class="text-muted small mb-0">Supply Chain &amp; Inventory Management</p>
    </div>

    <div class="p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show small" role="alert">
                <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                <i class="fa-solid fa-circle-xmark me-1"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-regular fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control border-start-0" placeholder="name@restaurant.com" value="{{ old('email', 'manager@restaurant.com') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" value="password" required>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember" checked>
                    <label class="form-check-label small text-muted" for="remember">Remember me</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="fa-solid fa-right-to-bracket me-2"></i>Sign In
            </button>
        </form>

        <!-- Quick 1-Click Demo Login -->
        <div class="demo-box mt-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-bold text-muted text-uppercase" style="font-size:0.7rem; letter-spacing:0.05em;">
                    <i class="fa-solid fa-bolt text-warning me-1"></i>Quick 1-Click Role Login
                </span>
            </div>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('login.quick', 'manager') }}" class="btn btn-outline-primary btn-demo bg-white">
                    <span>
                        <i class="fa-solid fa-user-tie me-2 text-primary"></i>
                        <strong>Manager:</strong> Sarah Ahmed
                    </span>
                    <span class="badge bg-primary">manager</span>
                </a>
                <a href="{{ route('login.quick', 'staff') }}" class="btn btn-outline-secondary btn-demo bg-white">
                    <span>
                        <i class="fa-solid fa-user-pen me-2 text-secondary"></i>
                        <strong>Staff:</strong> Rahim Mia
                    </span>
                    <span class="badge bg-secondary">staff</span>
                </a>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
