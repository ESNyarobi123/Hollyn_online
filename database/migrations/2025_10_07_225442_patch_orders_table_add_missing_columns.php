<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            // plan_id
            if (!Schema::hasColumn('orders', 'plan_id')) {
                $t->foreignId('plan_id')->after('id')->constrained()->cascadeOnDelete();
            }

            // user_id (safety – tulishaiweka, lakini kama haipo)
            if (!Schema::hasColumn('orders', 'user_id')) {
                $t->foreignId('user_id')->nullable()->after('plan_id')->constrained()->nullOnDelete();
            }

            // order_uuid
            if (!Schema::hasColumn('orders', 'order_uuid')) {
                $t->uuid('order_uuid')->unique()->after('user_id');
            } else {
                try { $t->unique('order_uuid'); } catch (\Throwable $e) {}
            }

            // customer fields
            if (!Schema::hasColumn('orders', 'customer_name'))  { $t->string('customer_name')->after('order_uuid'); }
            if (!Schema::hasColumn('orders', 'customer_email')) { $t->string('customer_email')->after('customer_name'); }
            if (!Schema::hasColumn('orders', 'customer_phone')) { $t->string('customer_phone', 32)->after('customer_email'); }

            // domain (nullable)
            if (!Schema::hasColumn('orders', 'domain')) {
                $t->string('domain')->nullable()->after('customer_phone');
            }

            // price/currency
            if (!Schema::hasColumn('orders', 'price_tzs')) { $t->unsignedBigInteger('price_tzs')->default(0)->after('domain'); }
            if (!Schema::hasColumn('orders', 'currency'))  { $t->string('currency', 8)->default('TZS')->after('price_tzs'); }

            // status
            if (!Schema::hasColumn('orders', 'status')) {
                $t->enum('status', ['pending','paid','failed','provisioning','active','suspended','cancelled'])
                  ->default('pending')->after('currency');
            }

            // payment_ref
            if (!Schema::hasColumn('orders', 'payment_ref')) {
                $t->string('payment_ref')->nullable()->after('status');
            }

            // timestamps
            $hasCreated = Schema::hasColumn('orders', 'created_at');
            $hasUpdated = Schema::hasColumn('orders', 'updated_at');
            if (!$hasCreated && !$hasUpdated) {
                $t->timestamps();
            } else {
                if (!$hasCreated) { $t->timestamp('created_at')->nullable(); }
                if (!$hasUpdated) { $t->timestamp('updated_at')->nullable(); }
            }

            // index helpful
            try { $t->index(['status', 'created_at']); } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        // hatufuti columns kwa rollback ili kulinda data
    }
};
