<?php

namespace Database\Seeders;

use App\Models\PartnerPackage;
use Illuminate\Database\Seeder;

class PartnerPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Basic',
                'description' => 'Perfect for small restaurants starting out. Get your menu online and start receiving orders.',
                'price' => 29.99,
                'billing_cycle' => 'monthly',
                'includes_ads' => false,
                'max_menu_items' => 50,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'description' => 'Grow your business with featured listings and basic advertising tools.',
                'price' => 59.99,
                'billing_cycle' => 'monthly',
                'includes_ads' => true,
                'max_menu_items' => 100,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Maximum visibility and advanced analytics for established restaurants.',
                'price' => 99.99,
                'billing_cycle' => 'monthly',
                'includes_ads' => true,
                'max_menu_items' => -1,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Basic (Yearly)',
                'description' => 'Basic plan with yearly billing - save 20%',
                'price' => 287.90,
                'billing_cycle' => 'yearly',
                'includes_ads' => false,
                'max_menu_items' => 50,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Pro (Yearly)',
                'description' => 'Pro plan with yearly billing - save 20%',
                'price' => 575.90,
                'billing_cycle' => 'yearly',
                'includes_ads' => true,
                'max_menu_items' => 100,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise (Yearly)',
                'description' => 'Enterprise plan with yearly billing - save 20%',
                'price' => 959.90,
                'billing_cycle' => 'yearly',
                'includes_ads' => true,
                'max_menu_items' => -1,
                'is_featured' => false,
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            PartnerPackage::create($package);
        }
    }
}