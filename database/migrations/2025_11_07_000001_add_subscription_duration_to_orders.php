<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add subscription duration support
            $table->unsignedInteger('duration_months')->default(1)->after('price_tzs');
            $table->unsignedBigInteger('base_price_monthly')->nullable()->after('duration_months');
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('base_price_monthly');
            
            // Add notes for customer
            $table->text('notes')->nullable()->after('domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['duration_months', 'base_price_monthly', 'discount_percentage', 'notes']);
        });
    }
};

