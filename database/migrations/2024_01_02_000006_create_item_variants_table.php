<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('price_modifier', 10, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->index(['menu_item_id', 'is_available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_variants');
    }
};