<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('services')) return;

        // Ikiwa status ni TINYINT/INT, hamiisha values 0/1 -> strings kabla ya ku-change
        try {
            // map ya kawaida: 1->active, 0->requested
            DB::table('services')->where('status', 1)->update(['status' => 'active']);
            DB::table('services')->where('status', 0)->update(['status' => 'requested']);
        } catch (\Throwable $e) {
            // ignore kama sio nambari
        }

        Schema::table('services', function (Blueprint $t) {
            // Badilisha kuwa string(32) + index + default 'requested'
            $t->string('status', 32)->default('requested')->index()->change();
        });
    }

    public function down(): void
    {
        // Optional: rudi tinyint au enum yako ya zamani kama unataka
    }
};
