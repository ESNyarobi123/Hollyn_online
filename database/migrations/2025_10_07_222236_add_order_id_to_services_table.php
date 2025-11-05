<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ongeza order_id kama haipo
        if (!Schema::hasColumn('services', 'order_id')) {
            Schema::table('services', function (Blueprint $t) {
                $t->foreignId('order_id')
                    ->after('id')
                    ->constrained()      // references orders(id)
                    ->cascadeOnDelete(); // ukifuta order, service ifutike
            });
        }

        // Ongeza status kama haipo
        if (!Schema::hasColumn('services', 'status')) {
            Schema::table('services', function (Blueprint $t) {
                $t->enum('status', ['pending_provision','active','suspended'])->default('pending_provision');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('services', 'order_id')) {
            Schema::table('services', function (Blueprint $t) {
                $t->dropForeign(['order_id']);
                $t->dropColumn('order_id');
            });
        }

        if (Schema::hasColumn('services', 'status')) {
            Schema::table('services', function (Blueprint $t) {
                $t->dropColumn('status');
            });
        }
    }
};
