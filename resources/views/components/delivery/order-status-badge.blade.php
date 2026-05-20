@props(['status'])

@php
    $map = [
        'placed' => ['label' => 'Placed', 'class' => 'bg-secondary'],
        'confirmed' => ['label' => 'Confirmed', 'class' => 'bg-info text-dark'],
        'preparing' => ['label' => 'Preparing', 'class' => 'bg-warning text-dark'],
        'on_the_way' => ['label' => 'On the way', 'class' => 'bg-primary'],
        'delivered' => ['label' => 'Delivered', 'class' => 'bg-success'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-danger'],
    ];
    $meta = $map[$status] ?? ['label' => ucfirst(str_replace('_', ' ', (string) $status)), 'class' => 'bg-secondary'];
@endphp

<span {{ $attributes->merge(['class' => 'badge rounded-pill '.$meta['class']]) }}>{{ $meta['label'] }}</span>
