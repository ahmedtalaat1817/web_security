<div class="nav-item">
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('admin.partners.index') }}" class="nav-link {{ request()->routeIs('admin.partners.*') ? 'active' : '' }}">
        <i class="bi bi-people"></i> Partners
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('admin.packages.index') }}" class="nav-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
        <i class="bi bi-box-seam"></i> Packages
    </a>
</div>
<div class="nav-item mt-3">
    <a href="{{ route('home') }}" class="nav-link">
        <i class="bi bi-arrow-left"></i> Back to Home
    </a>
</div>