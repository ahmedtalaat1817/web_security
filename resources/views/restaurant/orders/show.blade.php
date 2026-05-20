@extends('layouts.app')
@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="container py-4">
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @error('order')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h2 class="mb-1">Order {{ $order->order_number }}</h2>
            <span class="status-badge status-{{ $order->status }}">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
        </div>
        <a href="{{ route('restaurant.orders') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to orders
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Customer &amp; delivery</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                    <p class="mb-1 text-muted"><i class="fas fa-phone"></i> {{ $order->customer_phone }}</p>
                    <p class="mb-0"><i class="fas fa-map-marker-alt text-primary"></i> {{ $order->delivery_address }}</p>
                    @if($order->delivery_instructions)
                        <p class="mt-2 mb-0 small text-muted">{{ $order->delivery_instructions }}</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Items</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Line</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $line)
                            <tr>
                                <td>
                                    {{ $line->item_name }}
                                    @if($line->variant_name)
                                        <div class="small text-muted">{{ $line->variant_name }}</div>
                                    @endif
                                    @if($line->special_instructions)
                                        <div class="small text-muted"><em>{{ $line->special_instructions }}</em></div>
                                    @endif
                                </td>
                                <td class="text-center">{{ $line->quantity }}</td>
                                <td class="text-end">${{ number_format($line->unit_price, 2) }}</td>
                                <td class="text-end">${{ number_format($line->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Subtotal</th>
                                <th class="text-end">${{ number_format($order->subtotal, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Fees / delivery</th>
                                <th class="text-end">${{ number_format($order->delivery_fee ?? 0, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th class="text-end">${{ number_format($order->total, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    @if($order->status === 'placed')
                        <form method="POST" action="{{ route('restaurant.orders.confirm', $order) }}" class="d-grid mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success">Confirm order</button>
                        </form>
                    @elseif($order->status === 'confirmed')
                        <form method="POST" action="{{ route('restaurant.orders.preparing', $order) }}" class="d-grid mb-2">
                            @csrf
                            <button type="submit" class="btn btn-primary">Start preparing</button>
                        </form>
                    @else
                        <p class="text-muted small mb-0">No restaurant actions available for this status. Next steps are handled by riders or admins.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
