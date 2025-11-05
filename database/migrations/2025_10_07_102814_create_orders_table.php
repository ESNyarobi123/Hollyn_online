<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $t->string('order_uuid')->unique(); // kwa ZenoPay order_id
            $t->string('customer_name');
            $t->string('customer_email');
            $t->string('customer_phone'); // 06/07xxxxxxxx
            $t->string('domain')->nullable();
            $t->unsignedBigInteger('price_tzs');
            $t->string('currency', 8)->default(env('APP_CURRENCY', 'TZS'));
            $t->enum('status', ['pending','paid','failed','provisioning','active','suspended','cancelled'])->default('pending');
            $t->string('payment_ref')->nullable(); // transid/ref kutoka gateway
            $t->timestamps();
            $t->index(['status','created_at']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
