<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('stripe_connect_account_id')->nullable()->after('email');
        });

        Schema::table('riders', function (Blueprint $table) {
            $table->string('stripe_connect_account_id')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('stripe_connect_account_id');
        });

        Schema::table('riders', function (Blueprint $table) {
            $table->dropColumn('stripe_connect_account_id');
        });
    }
};
