<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'panel_url')) {
                $table->string('panel_url')->nullable()->after('enduser_url');
            }
        });
    }
    public function down(): void {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'panel_url')) {
                $table->dropColumn('panel_url');
            }
        });
    }
};
