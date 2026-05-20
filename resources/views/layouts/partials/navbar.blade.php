<nav class="navbar">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <div class="brand-icon me-2">
                <i class="bi bi-lightning-charge-fill"></i>
            </div>
            <span class="brand-text">Quickbite!</span>
        </a>

        <ul class="nav-links-desktop">
            <li><a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
            <li><a class="{{ request()->routeIs('restaurants.index') ? 'active' : '' }}" href="{{ route('restaurants.index') }}">Restaurants</a></li>
            @guest
            <li><a class="{{ request()->routeIs('partner.pricing') ? 'active' : '' }}" href="{{ route('partner.pricing') }}">Become a Partner</a></li>
            @else
            @php $user = Auth::user(); @endphp
            @if(!$user->isRestaurant() && !$user->isRider())
            <li><a class="{{ request()->routeIs('partner.pricing') ? 'active' : '' }}" href="{{ route('partner.pricing') }}">Become a Partner</a></li>
            @endif
            @endguest
            @auth
            @php $user = Auth::user(); @endphp
            @if($user->isAdmin())
            <li><a href="{{ route('admin.dashboard') }}"><span class="badge badge-primary">Admin</span></a></li>
            @endif
            @if(!$user->isCustomer() && !$user->isAdmin())
            <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
            @endif
            @if($user->isCustomer())
            <li><a href="{{ route('customer.orders.index') }}">Orders</a></li>
            @endif
            @endauth
        </ul>

        <div class="nav-actions-desktop">
            @auth
                @php
                    $user = Auth::user();
                    $notifications = [];
                    $notifCount = 0;
                    if ($user->isRestaurant()) {
                        $restaurant = $user->restaurant;
                        if ($restaurant) {
                            $newOrders = $restaurant->orders()->where('status', 'confirmed')->latest()->take(5)->get();
                            foreach ($newOrders as $o) {
                                $notifications[] = ['icon' => 'bi-bag', 'text' => "New order {$o->order_number}", 'time' => $o->created_at->diffForHumans(), 'url' => route('restaurant.orders.show', $o->id)];
                            }
                            $notifCount = $newOrders->count();
                        }
                    } elseif ($user->isAdmin()) {
                        $pendingOrders = \App\Models\Order::where('status', 'confirmed')->whereNull('rider_id')->take(5)->get();
                        foreach ($pendingOrders as $o) {
                            $notifications[] = ['icon' => 'bi-clock', 'text' => "Order {$o->order_number} needs rider", 'time' => $o->created_at->diffForHumans(), 'url' => route('admin.dashboard')];
                        }
                        $pendingRestaurants = \App\Models\Restaurant::where('is_open', false)->take(5)->get();
                        foreach ($pendingRestaurants as $r) {
                            $notifications[] = ['icon' => 'bi-shop', 'text' => "{$r->name} pending approval", 'time' => $r->created_at->diffForHumans(), 'url' => route('admin.dashboard')];
                        }
                        $notifCount = $pendingOrders->count() + $pendingRestaurants->count();
                    } elseif ($user->isRider()) {
                        $rider = $user->rider;
                        if ($rider) {
                            $newAssignments = $rider->orders()->whereIn('status', ['confirmed', 'preparing'])->latest()->take(5)->get();
                            foreach ($newAssignments as $o) {
                                $notifications[] = ['icon' => 'bi-bicycle', 'text' => "Order {$o->order_number} assigned", 'time' => $o->created_at->diffForHumans(), 'url' => route('rider.dashboard')];
                            }
                            $notifCount = $newAssignments->count();
                        }
                    } else {
                        $orderUpdates = $user->customerOrders()->whereIn('status', ['confirmed', 'preparing', 'on_the_way'])->latest()->take(5)->get();
                        foreach ($orderUpdates as $o) {
                            $notifications[] = ['icon' => 'bi-truck', 'text' => "Order {$o->order_number} is " . str_replace('_', ' ', $o->status), 'time' => $o->updated_at->diffForHumans(), 'url' => route('customer.orders.show', $o->id)];
                        }
                        $notifCount = $orderUpdates->count();
                    }
                @endphp
                <div class="dropdown">
                    <button class="btn nav-icon-btn position-relative" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        @if($notifCount > 0)
                        <span class="badge-notif">{{ $notifCount > 9 ? '9+' : $notifCount }}</span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <div class="px-3 py-2 fw-bold border-bottom">Notifications</div>
                        @forelse($notifications as $notif)
                        <a class="dropdown-item d-flex align-items-start gap-3 py-3 border-bottom">
                            <div class="notif-icon"><i class="bi {{ $notif['icon'] }}"></i></div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="small">{{ $notif['text'] }}</div>
                                <small class="text-muted">{{ $notif['time'] }}</small>
                            </div>
                        </a>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check2-circle d-block mb-2 fs-3"></i>
                            <small>No notifications</small>
                        </div>
                        @endforelse
                    </div>
                </div>

                <button class="btn nav-icon-btn" onclick="toggleTheme()">
                    <i class="bi bi-moon-stars" id="theme-icon-desk"></i>
                </button>

                <div class="dropdown">
                    <button class="btn user-menu-btn" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar-nav"><i class="bi bi-person-fill"></i></div>
                        <span class="fw-semibold ms-1">{{ Auth::user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                        @if($user->isAdmin())
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-gear me-2"></i>Admin Panel</a></li>
                        @endif
                        @if(!$user->isCustomer() && !$user->isAdmin())
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger w-100"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary-custom btn-sm">Sign Up</a>
                <button class="btn nav-icon-btn" onclick="toggleTheme()">
                    <i class="bi bi-moon-stars" id="theme-icon-desk-guest"></i>
                </button>
            @endauth
        </div>

        <button class="navbar-toggler" onclick="toggleMobile()">
            <i class="bi bi-list" id="toggler-icon"></i>
        </button>
    </div>

    <div class="mobile-menu" id="mobileMenu">
        <a class="mobile-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
        <a class="mobile-link {{ request()->routeIs('restaurants.index') ? 'active' : '' }}" href="{{ route('restaurants.index') }}">Restaurants</a>
        @guest
        <a class="mobile-link {{ request()->routeIs('partner.pricing') ? 'active' : '' }}" href="{{ route('partner.pricing') }}">Become a Partner</a>
        @else
        @php $user = Auth::user(); @endphp
        @if(!$user->isRestaurant() && !$user->isRider())
        <a class="mobile-link {{ request()->routeIs('partner.pricing') ? 'active' : '' }}" href="{{ route('partner.pricing') }}">Become a Partner</a>
        @endif
        @endguest
        @auth
        @php $user = Auth::user(); @endphp
        @if($user->isAdmin())
        <a class="mobile-link" href="{{ route('admin.dashboard') }}"><span class="badge badge-primary">Admin</span></a>
        @endif
        @if(!$user->isCustomer() && !$user->isAdmin())
        <a class="mobile-link" href="{{ route('dashboard') }}">Dashboard</a>
        @endif
        @if($user->isCustomer())
        <a class="mobile-link" href="{{ route('customer.orders.index') }}">Orders</a>
        @endif
        <div class="mobile-actions">
            <button class="btn nav-icon-btn" onclick="toggleTheme()"><i class="bi bi-moon-stars" id="theme-icon-mob"></i></button>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm">Logout</button>
            </form>
        </div>
        @else
        <div class="mobile-actions">
            <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm flex-fill">Login</a>
            <a href="{{ route('register') }}" class="btn btn-primary-custom btn-sm flex-fill">Sign Up</a>
            <button class="btn nav-icon-btn" onclick="toggleTheme()"><i class="bi bi-moon-stars" id="theme-icon-mob-guest"></i></button>
        </div>
        @endauth
    </div>
</nav>

<style>
.navbar {
    background: var(--bg-secondary) !important;
    border-bottom: 1px solid var(--border-default);
    position: sticky;
    top: 0;
    z-index: 9999;
}

.container-fluid {
    display: flex;
    align-items: center;
    gap: 16px;
}

.brand-icon {
    width: 42px;
    height: 42px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.brand-text {
    font-family: var(--font-display);
    font-size: 22px;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.nav-links-desktop {
    display: none;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 4px;
}

.nav-links-desktop li a {
    display: inline-block;
    padding: 8px 16px;
    border-radius: var(--radius-md);
    font-weight: 500;
    font-size: 14px;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all 150ms ease;
}

.nav-links-desktop li a:hover {
    color: var(--primary);
    background: rgba(59, 130, 246, 0.08);
}

.nav-links-desktop li a.active {
    color: var(--primary);
    background: rgba(59, 130, 246, 0.1);
    font-weight: 600;
}

.nav-actions-desktop {
    display: none;
    align-items: center;
    gap: 8px;
    margin-left: auto;
}

.nav-icon-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    border: 1px solid var(--border-default);
    background: var(--bg-tertiary);
    color: var(--text-secondary);
    padding: 0;
    cursor: pointer;
    transition: all 150ms ease;
}

.nav-icon-btn:hover {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.user-avatar-nav {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.user-menu-btn {
    padding: 6px 12px;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-default);
    background: var(--bg-tertiary);
    display: flex;
    align-items: center;
    color: var(--text-primary);
    cursor: pointer;
}

.user-menu-btn:hover {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

.user-menu-btn:hover .user-avatar-nav {
    background: white;
    color: var(--primary);
}

.user-menu-btn::after { display: none !important; }

.badge-notif {
    position: absolute;
    top: -4px;
    right: -4px;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: 700;
    border-radius: 50px;
    background: var(--danger);
    color: white;
    line-height: 1.2;
}

.notif-icon {
    width: 36px;
    height: 36px;
    background: rgba(59, 130, 246, 0.12);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    flex-shrink: 0;
}

.dropdown-menu {
    border: 1px solid var(--border-default);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-xl);
    padding: 8px;
}

.dropdown-item {
    padding: 10px 16px;
    border-radius: var(--radius-md);
    color: var(--text-secondary);
    font-weight: 500;
}

.dropdown-item:hover {
    background: var(--surface-hover);
    color: var(--text-primary);
}

.navbar-toggler {
    border: 1px solid var(--border-default);
    background: var(--bg-tertiary);
    margin-left: auto;
    padding: 8px 12px;
    cursor: pointer;
}

.navbar-toggler:focus { box-shadow: none; }

.mobile-menu {
    display: none !important;
    background: var(--bg-secondary);
    border-top: 1px solid var(--border-default);
    padding: 8px 0;
    width: 100%;
    position: absolute;
    left: 0;
    top: 100%;
    z-index: 9998;
}

.mobile-menu.open {
    display: flex !important;
    flex-direction: column;
}

.mobile-link {
    display: block;
    padding: 14px 20px;
    color: var(--text-secondary);
    font-weight: 500;
    font-size: 15px;
    text-decoration: none;
    border-bottom: 1px solid var(--border-subtle);
}

.mobile-link.active {
    color: var(--primary);
    font-weight: 600;
    background: rgba(59, 130, 246, 0.05);
}

.mobile-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 16px 20px;
    justify-content: center;
}

@media (min-width: 992px) {
    .nav-links-desktop {
        display: flex !important;
    }

    .nav-actions-desktop {
        display: flex !important;
    }

    .navbar-toggler {
        display: none !important;
    }

    .mobile-menu {
        display: none !important;
    }
}
</style>
<script>
function toggleMobile() {
    var menu = document.getElementById('mobileMenu');
    menu.classList.toggle('open');
    var icon = document.getElementById('toggler-icon');
    if (icon) {
        icon.className = menu.classList.contains('open') ? 'bi bi-x-lg' : 'bi bi-list';
    }
}

(function() {
    var theme = document.documentElement.getAttribute('data-theme');
    document.querySelectorAll('[id^="theme-icon-"]').forEach(function(el) {
        el.className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
    });
})();
</script>