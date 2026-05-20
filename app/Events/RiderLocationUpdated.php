<?php

namespace App\Events;

use App\Models\Rider;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RiderLocationUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Rider $rider,
        public float $latitude,
        public float $longitude
    ) {}
}