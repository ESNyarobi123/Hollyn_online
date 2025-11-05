<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('services')) return;


        try {
            DB::table('services')->where('status', 1)->update(['status' => 'active']);
            DB::table('services')->where('status', 0)->update(['status' => 'requested']);
        } catch (\Throwable $e) {}

        DB::statement("ALTER TABLE `services`
            MODIFY `status` VARCHAR(32) NOT NULL DEFAULT 'requested'");
    }

    public function down(): void
    {
        // optional: rudi aina ya zamani
    }
};
