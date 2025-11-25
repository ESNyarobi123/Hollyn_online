<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'price_tzs')) {
                $table->unsignedBigInteger('price_tzs')->default(0)->after('domain');
            }
            if (!Schema::hasColumn('orders', 'duration_months')) {
                $table->unsignedInteger('duration_months')->default(1)->after('price_tzs');
            }
            if (!Schema::hasColumn('orders', 'base_price_monthly')) {
                $table->unsignedBigInteger('base_price_monthly')->nullable()->after('duration_months');
            }
            if (!Schema::hasColumn('orders', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0)->after('base_price_monthly');
            }
            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('domain');
            }
        });
    }

    public function down(): void
    {
        // No-op to avoid data loss
    }
};
