<?php

return [

    'strategy' => env('SURGE_STRATEGY', 'multiplier'),

    'max_multiplier' => (float) env('SURGE_MAX_MULTIPLIER', 2.5),

    'max_extra_delivery' => (float) env('SURGE_MAX_EXTRA_DELIVERY', 8.0),

    'flat_extra' => (float) env('SURGE_FLAT_EXTRA', 1.5),

    'time_windows' => [
        ['start' => 11, 'end' => 14, 'multiplier' => 1.15],
        ['start' => 18, 'end' => 22, 'multiplier' => 1.25],
    ],

];
