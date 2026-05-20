<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->enum('old_state', ['placed', 'confirmed', 'preparing', 'on_the_way', 'delivered', 'cancelled'])->nullable();
            $table->enum('new_state', ['placed', 'confirmed', 'preparing', 'on_the_way', 'delivered', 'cancelled']);
            $table->foreignId('actor_id')->constrained('users')->onDelete('cascade');
            $table->string('actor_type');
            $table->text('notes')->nullable();
            $table->timestamp('timestamp');

            $table->index(['order_id', 'timestamp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_logs');
    }
};