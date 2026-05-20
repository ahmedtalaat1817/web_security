<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('owner_name')->nullable()->after('name');
            $table->string('national_id')->nullable()->after('owner_name');
            $table->string('commercial_registration_number')->nullable()->after('national_id');
            $table->string('tax_id')->nullable()->after('commercial_registration_number');
            $table->string('restaurant_name')->nullable()->after('tax_id');
            $table->string('restaurant_address')->nullable()->after('restaurant_name');
            $table->unsignedBigInteger('partner_package_id')->nullable()->after('restaurant_address');
            $table->enum('partner_status', ['pending', 'pending_payment', 'active', 'suspended'])->default('pending')->after('partner_package_id');
            $table->string('payment_id')->nullable()->after('partner_status');
            $table->timestamp('partner_since')->nullable()->after('payment_id');
            $table->foreign('partner_package_id')->references('id')->on('partner_packages')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['partner_package_id']);
            $table->dropColumn([
                'owner_name',
                'national_id',
                'commercial_registration_number',
                'tax_id',
                'restaurant_name',
                'restaurant_address',
                'partner_package_id',
                'partner_status',
                'payment_id',
                'partner_since',
            ]);
        });
    }
};