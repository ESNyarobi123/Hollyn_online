<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_events', function (Blueprint $t) {
            // order_id → FK kwa orders(id)
            if (!Schema::hasColumn('payment_events', 'order_id')) {
                $t->foreignId('order_id')->after('id')->constrained()->cascadeOnDelete();
            }

            // gateway (mf. 'zenopay')
            if (!Schema::hasColumn('payment_events', 'gateway')) {
                $t->string('gateway', 50)->default('zenopay');
            }

            // direction (request/webhook/callback)
            if (!Schema::hasColumn('payment_events', 'direction')) {
                $t->string('direction', 50)->default('request');
            }

            // payload (json cha response/webhook)
            if (!Schema::hasColumn('payment_events', 'payload')) {
                $t->json('payload')->nullable();
            }

            // status (mf. success/completed/failed)
            if (!Schema::hasColumn('payment_events', 'status')) {
                $t->string('status', 50)->nullable();
            }

            // timestamps
            $hasCreated = Schema::hasColumn('payment_events', 'created_at');
            $hasUpdated = Schema::hasColumn('payment_events', 'updated_at');
            if (!$hasCreated && !$hasUpdated) {
                $t->timestamps();
            } else {
                if (!$hasCreated) { $t->timestamp('created_at')->nullable(); }
                if (!$hasUpdated) { $t->timestamp('updated_at')->nullable(); }
            }

            // index helpful
            try { $t->index(['order_id','gateway','direction']); } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        // hatufuti columns kwa rollback ili kulinda data
    }
};
