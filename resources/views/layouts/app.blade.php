<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Grievance Portal' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f0f2f5; color: #1a1a2e; }

        /* ── Navbar ─────────────────────────────────── */
        .navbar { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: white; padding: 0 40px; display: flex; align-items: center; justify-content: space-between; height: 60px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); position: sticky; top: 0; z-index: 100; }
        .nav-left { display: flex; align-items: center; gap: 32px; }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: white; }
        .brand-icon { width: 32px; height: 32px; background: #e94560; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; }
        .brand-text { font-size: 17px; font-weight: 700; letter-spacing: -0.3px; }
        .nav-menu { display: flex; gap: 4px; }
        .nav-menu a { color: white; text-decoration: none; font-size: 14px; font-weight: 500; padding: 8px 14px; border-radius: 6px; transition: background 0.2s; }
        .nav-menu a:hover, .nav-menu a.active { background: rgba(255,255,255,0.12); }

        .nav-right { display: flex; align-items: center; gap: 16px; }
        .user-info { display: flex; align-items: center; gap: 10px; }
        .avatar { width: 34px; height: 34px; border-radius: 50%; background: #e94560; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; color: white; }
        .user-details { line-height: 1.3; }
        .user-name { font-size: 13px; font-weight: 600; }
        .user-role { font-size: 11px; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.5px; }
        .divider { width: 1px; height: 28px; background: rgba(255,255,255,0.2); }
        .logout-btn { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 6px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; transition: background 0.2s; }
        .logout-btn:hover { background: rgba(233,69,96,0.8); border-color: transparent; }

        /* ── Container ──────────────────────────────── */
        .container { max-width: 960px; margin: 28px auto; padding: 0 20px; }

        /* ── Cards ──────────────────────────────────── */
        .card { background: white; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); padding: 24px; margin-bottom: 20px; }
        .card h2 { margin-bottom: 16px; font-size: 20px; }

        /* ── Stats ──────────────────────────────────── */
        .stats { display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
        .stat { background: white; border-radius: 10px; padding: 16px 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); min-width: 120px; flex: 1; }
        .stat h3 { font-size: 28px; color: #e94560; }
        .stat p { font-size: 13px; color: #666; margin-top: 2px; }

        /* ── Table ──────────────────────────────────── */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px 12px; border-bottom: 2px solid #eee; font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 10px 12px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        tr:hover { background: #f8f9fa; }
        td a { color: #0077b6; text-decoration: none; font-weight: 500; }
        td a:hover { text-decoration: underline; }

        /* ── Badges ─────────────────────────────────── */
        .badge { padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; display: inline-block; }
        .badge-open { background: #fff3cd; color: #856404; }
        .badge-in_progress { background: #cce5ff; color: #004085; }
        .badge-resolved { background: #d4edda; color: #155724; }
        .badge-rejected { background: #f8d7da; color: #721c24; }

        /* ── Buttons ────────────────────────────────── */
        .btn { display: inline-block; padding: 8px 20px; border-radius: 6px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; transition: background 0.2s; }
        .btn-primary { background: #0077b6; color: white; }
        .btn-primary:hover { background: #005f8d; }
        .btn-danger { background: #e94560; color: white; }
        .btn-danger:hover { background: #c3374e; }
        .btn-sm { padding: 5px 14px; font-size: 12px; }

        /* ── Forms ──────────────────────────────────── */
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #444; margin-bottom: 5px; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; font-family: inherit;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline: none; border-color: #0077b6; box-shadow: 0 0 0 3px rgba(0,119,182,0.1); }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .form-error { color: #e94560; font-size: 12px; margin-top: 4px; }

        /* ── Alerts ─────────────────────────────────── */
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; display: flex; align-items: center; gap: 8px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .validation-errors { background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; padding: 14px 18px; margin-bottom: 16px; }
        .validation-errors p { font-weight: 600; color: #721c24; margin-bottom: 6px; font-size: 14px; }
        .validation-errors ul { margin: 0; padding-left: 18px; }
        .validation-errors li { color: #721c24; font-size: 13px; margin-bottom: 3px; }

        /* ── Comments ───────────────────────────────── */
        .comment { padding: 12px 0; border-bottom: 1px solid #f0f0f0; }
        .comment:last-child { border-bottom: none; }
        .comment-meta { font-size: 12px; color: #888; margin-bottom: 4px; }
        .comment-meta strong { color: #333; }
        .comment-body { font-size: 14px; line-height: 1.5; }

        /* ── Detail ─────────────────────────────────── */
        .detail-grid { display: grid; grid-template-columns: 140px 1fr; gap: 8px 16px; font-size: 14px; }
        .detail-grid dt { font-weight: 600; color: #666; }
        .detail-grid dd { color: #1a1a2e; }

        .section-title { font-size: 16px; font-weight: 700; margin: 24px 0 12px; padding-bottom: 8px; border-bottom: 2px solid #eee; }

        .empty { text-align: center; padding: 40px; color: #888; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="/dashboard" class="brand">
                <div class="brand-icon">G</div>
                <span class="brand-text">Grievance Portal</span>
            </a>
            <div class="nav-menu">
                <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">Dashboard</a>
                @unless (auth()->user()->isAdmin())
                    <a href="/complaints/create" class="{{ request()->is('complaints/create') ? 'active' : '' }}">File Complaint</a>
                @endunless
            </div>
        </div>

        <div class="nav-right">
            <div class="user-info">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="user-details">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->role->value }}</div>
                </div>
            </div>
            <div class="divider"></div>
            <form action="/logout" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        {{-- Success messages --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Error messages --}}
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        {{-- Validation errors (shows on any page after bad form submission) --}}
        @if ($errors->any())
            <div class="validation-errors">
                <p>Please fix the following errors:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
