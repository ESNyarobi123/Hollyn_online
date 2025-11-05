<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ongeza kolamu kama haipo
        if (!Schema::hasColumn('orders', 'user_id')) {
            Schema::table('orders', function (Blueprint $t) {
                $t->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained()         // references users(id)
                    ->nullOnDelete();       // kama user afutwa, weka NULL
            });
        }

        // (Optional) hakikisha index ya status ipo, si lazima
        if (!Schema::hasColumn('orders', 'status')) {
            Schema::table('orders', function (Blueprint $t) {
                $t->enum('status', ['pending','paid','failed','provisioning','active','suspended','cancelled'])
                  ->default('pending');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'user_id')) {
            Schema::table('orders', function (Blueprint $t) {
                $t->dropForeign(['user_id']);
                $t->dropColumn('user_id');
            });
        }
    }
};
