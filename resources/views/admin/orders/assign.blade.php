@extends('layouts.dashboard')
@section('page_title', 'Assign Rider')

@section('sidebar_menu')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Assign Rider to Order {{ $order->order_number }}</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Available Riders</h5>
                </div>
                <div class="card-body">
                    @forelse($availableRiders as $rider)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                        <div>
                            <strong>{{ $rider->user->name }}</strong>
                            <span class="text-muted ms-2">
                                <i class="fas fa-star text-warning"></i> {{ number_format($rider->rating, 1) }}
                            </span>
                        </div>
                        <form method="POST" action="{{ route('admin.orders.assign', $order->id) }}">
                            @csrf
                            <input type="hidden" name="rider_id" value="{{ $rider->id }}">
                            <button type="submit" class="btn btn-primary btn-sm">Assign</button>
                        </form>
                    </div>
                    @empty
                    <p class="text-muted text-center">No available riders</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection