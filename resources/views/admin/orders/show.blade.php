@extends('layouts.dashboard')
@section('page_title', 'Order ' . $order->order_number)

@section('sidebar_menu')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Order {{ $order->order_number }}</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
                            <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                            <p><strong>Address:</strong> {{ $order->delivery_address }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Restaurant:</strong> {{ $order->restaurant->name }}</p>
                            <p><strong>Rider:</strong> {{ $order->rider?->user->name ?? 'Unassigned' }}</p>
                            <p><strong>Status:</strong> <span class="status-badge status-{{ $order->status }}">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection