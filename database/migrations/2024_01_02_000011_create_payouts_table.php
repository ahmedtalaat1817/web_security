<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->enum('recipient_type', ['restaurant', 'rider']);
            $table->foreignId('recipient_id');
            $table->string('stripe_transfer_id')->nullable();
            $table->string('stripe_connect_account_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('platform_commission', 10, 2)->default(0);
            $table->enum('status', ['pending', 'processing', 'succeeded', 'failed'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['recipient_type', 'recipient_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};