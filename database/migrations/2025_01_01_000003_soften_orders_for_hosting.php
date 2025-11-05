<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('orders')) return;

        Schema::table('orders', function (Blueprint $t) {
            if (!Schema::hasColumn('orders','status'))     $t->string('status', 32)->default('pending')->index()->after('id');
            if (!Schema::hasColumn('orders','price_tzs'))  $t->unsignedBigInteger('price_tzs')->default(0)->after('status');
            if (!Schema::hasColumn('orders','plan_id'))    $t->unsignedBigInteger('plan_id')->nullable()->after('price_tzs');
            if (!Schema::hasColumn('orders','user_id'))    $t->unsignedBigInteger('user_id')->nullable()->after('plan_id');

            // helpful indexes
            $t->index(['user_id','status'],'orders_user_status_idx');
            $t->index(['plan_id'],'orders_plan_idx');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) return;

        Schema::table('orders', function (Blueprint $t) {
            foreach (['orders_user_status_idx','orders_plan_idx'] as $idx) {
                try { $t->dropIndex($idx); } catch (\Throwable $e) {}
            }
            foreach (['status','price_tzs','plan_id','user_id'] as $col) {
                try { $t->dropColumn($col); } catch (\Throwable $e) {}
            }
        });
    }
};
