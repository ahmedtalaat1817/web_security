@extends('layouts.dashboard')
@section('page_title', 'Manage Menu')
@section('sidebar_menu')
<div class="nav-item">
    <a href="{{ route('restaurant.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('restaurant.orders') }}" class="nav-link">
        <i class="bi bi-bag"></i> Orders
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('restaurant.menu.index') }}" class="nav-link active">
        <i class="bi bi-menu-button-wide"></i> Menu
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('restaurant.menu.create') }}" class="nav-link">
        <i class="bi bi-plus-circle"></i> Add Item
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('restaurant.profile') }}" class="nav-link">
        <i class="bi bi-shop"></i> Restaurant Profile
    </a>
</div>
<div class="nav-item mt-3">
    <a href="{{ route('home') }}" class="nav-link">
        <i class="bi bi-arrow-left"></i> Back to Home
    </a>
</div>
@endsection

@section('styles')
<style>
    .menu-header {
        background: linear-gradient(135deg, var(--dark-charcoal), var(--dark-secondary));
        padding: 32px;
        border-radius: var(--radius-lg);
        margin-bottom: 24px;
    }
    .category-card {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        margin-bottom: 20px;
    }
    .category-header {
        padding: 16px 20px;
        background: var(--bg-tertiary);
        font-weight: 700;
        font-size: 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 20px;
        border-bottom: 1px solid var(--light-gray);
        transition: var(--transition-fast);
    }
    .item-row:last-child {
        border-bottom: none;
    }
    .item-row:hover {
        background: var(--surface-hover);
    }
    .item-info {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }
    .item-thumb {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        object-fit: cover;
        background: var(--bg-tertiary);
    }
    .item-thumb-placeholder {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        background: var(--bg-tertiary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted-gray);
    }
    .item-name {
        font-weight: 600;
    }
    .item-price {
        color: var(--primary);
        font-weight: 700;
    }
    .badge-unavailable {
        background: var(--danger-light);
        color: var(--danger);
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 50px;
    }
</style>
@endsection

@section('content')
@if(session('status'))
<div class="alert alert-success border-0 rounded-3 mb-4">{{ session('status') }}</div>
@endif

<div class="menu-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="text-white mb-1" style="font-family: 'Playfair Display', serif;">{{ $restaurant->name }}</h3>
            <p class="text-white-50 mb-0">Manage your menu items</p>
        </div>
        <a href="{{ route('restaurant.menu.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-2"></i>Add Item
        </a>
    </div>
</div>

@forelse($categories as $category)
<div class="category-card">
    <div class="category-header">
        <span>{{ $category->name }}</span>
        <span class="badge bg-secondary">{{ $category->menuItems->count() }} items</span>
    </div>
    @forelse($category->menuItems as $item)
    <div class="item-row">
        <div class="item-info">
            @if($item->image)
            <img src="{{ $item->image }}" alt="{{ $item->name }}" class="item-thumb">
            @else
            <div class="item-thumb-placeholder">
                <i class="bi bi-image"></i>
            </div>
            @endif
            <div>
                <div class="item-name">{{ $item->name }}</div>
                <div class="small text-muted">{{ Str::limit($item->description, 60) }}</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end">
                <div class="item-price">${{ number_format($item->price, 2) }}</div>
                @if(!$item->is_available)
                <span class="badge-unavailable">Unavailable</span>
                @endif
            </div>
            <div class="d-flex gap-1">
                <a href="{{ route('restaurant.menu.edit', $item->id) }}" class="btn btn-sm btn-outline-custom" title="Edit">
                    <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="{{ route('restaurant.menu.destroy', $item->id) }}" class="d-inline" onsubmit="return confirm('Delete {{ $item->name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center text-muted py-4">No items in this category</div>
    @endforelse
</div>
@empty
<div class="text-center py-5">
    <i class="bi bi-menu-button display-1 text-muted"></i>
    <h4 class="mt-3 text-muted">Your menu is empty</h4>
    <p class="text-muted">Add your first menu item to get started</p>
    <a href="{{ route('restaurant.menu.create') }}" class="btn btn-primary-custom mt-2">
        <i class="bi bi-plus-lg me-2"></i>Add Item
    </a>
</div>
@endforelse
@endsection