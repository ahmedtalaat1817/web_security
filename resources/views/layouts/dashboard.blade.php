<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Quickbite!') }} - Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css'])

    <style>
        .sidebar {
            width: 280px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-default);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
            transition: transform var(--transition-base);
        }

        [data-theme="dark"] .sidebar {
            background: var(--bg-secondary);
            border-right-color: var(--border-default);
        }

        .sidebar-header {
            padding: var(--space-6);
            border-bottom: 1px solid var(--border-subtle);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            text-decoration: none;
        }

        .brand-icon-sm {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            box-shadow: var(--shadow-md), 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .brand-text-sm {
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .sidebar-nav {
            padding: var(--space-4);
        }

        .sidebar-nav .nav-item {
            margin-bottom: var(--space-1);
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            padding: var(--space-3) var(--space-4);
            color: var(--text-tertiary);
            font-weight: 500;
            border-radius: var(--radius-md);
            transition: all var(--transition-fast);
            text-decoration: none;
        }

        .sidebar-nav .nav-link:hover {
            background: var(--surface-hover);
            color: var(--text-primary);
        }

        .sidebar-nav .nav-link.active {
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary);
            font-weight: 600;
        }

        .sidebar-nav .nav-link i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
            background: var(--bg-primary);
        }

        .top-bar {
            background: var(--bg-secondary);
            padding: var(--space-4) var(--space-8);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-subtle);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .page-title-bar h2 {
            font-size: var(--text-xl);
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: var(--space-4);
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: var(--text-sm);
            display: block;
        }

        .user-email {
            color: var(--text-muted);
            font-size: var(--text-xs);
            display: block;
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            box-shadow: var(--shadow-md), 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .page-content {
            padding: var(--space-8);
        }

        .theme-toggle-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-default);
            background: var(--bg-tertiary);
            color: var(--text-tertiary);
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .theme-toggle-btn:hover {
            background: var(--surface-hover);
            color: var(--text-primary);
            border-color: var(--border-strong);
        }

        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .page-content {
                padding: var(--space-6);
            }
        }

        @media (max-width: 576px) {
            .page-content {
                padding: var(--space-4);
            }

            .top-bar {
                padding: var(--space-4);
            }
        }
    </style>

    @yield('styles')
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('home') }}" class="sidebar-brand">
                    <div class="brand-icon-sm">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <span class="brand-text-sm">Quickbite!</span>
                </a>
            </div>

            <nav class="sidebar-nav">
                @yield('sidebar_menu')
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn d-lg-none p-2" onclick="toggleSidebar()" style="color: var(--text-primary);">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div class="page-title-bar">
                        <h2>@yield('page_title', 'Dashboard')</h2>
                    </div>
                </div>

                <div class="user-dropdown">
                    <div class="d-none d-md-block user-info">
                        <span class="user-name">{{ Auth::user()->name }}</span>
                        <span class="user-email">{{ Auth::user()->email }}</span>
                    </div>
                    <div class="user-avatar">
                        <i class="bi bi-person"></i>
                    </div>
                    <button class="theme-toggle-btn" onclick="toggleTheme()" title="Toggle theme">
                        <i class="bi bi-moon-stars" id="dash-theme-icon"></i>
                    </button>
                </div>
            </div>

            <!-- Page Content -->
            <div class="page-content">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            var theme = localStorage.getItem('foodie_theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
            var icon = document.getElementById('dash-theme-icon');
            if (icon) icon.className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
        })();
        function toggleTheme() {
            var html = document.documentElement;
            var current = html.getAttribute('data-theme');
            var next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('foodie_theme', next);
            var icon = document.getElementById('dash-theme-icon');
            if (icon) icon.className = next === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
        }
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }
    </script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>