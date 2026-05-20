<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stripe_payment_intent_id')->unique()->nullable();
            $table->string('stripe_charge_id')->nullable();
            $table->string('method')->default('card');
            $table->decimal('amount', 10, 2);
            $table->decimal('platform_fee', 10, 2)->default(0);
            $table->decimal('restaurant_amount', 10, 2)->default(0);
            $table->decimal('rider_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'processing', 'succeeded', 'failed', 'refunded'])->default('pending');
            $table->string('currency', 3)->default('USD');
            $table->string('customer_email')->nullable();
            $table->text('failure_reason')->nullable();
            $table->string('idempotency_key')->unique()->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index('stripe_payment_intent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};