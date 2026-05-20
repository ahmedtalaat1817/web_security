<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('rider_id')->nullable()->constrained('riders')->nullOnDelete();
            $table->tinyInteger('rating');
            $table->text('comment')->nullable();
            $table->enum('type', ['restaurant', 'rider', 'delivery']);
            $table->timestamps();

            $table->index(['restaurant_id', 'rating']);
            $table->index(['rider_id', 'rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};