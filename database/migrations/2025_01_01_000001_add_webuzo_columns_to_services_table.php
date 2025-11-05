<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('services')) return;

        Schema::table('services', function (Blueprint $t) {
            if (!Schema::hasColumn('services','user_id'))            $t->unsignedBigInteger('user_id')->nullable()->after('id');
            if (!Schema::hasColumn('services','plan_id'))            $t->unsignedBigInteger('plan_id')->nullable()->after('user_id');
            if (!Schema::hasColumn('services','order_id'))           $t->unsignedBigInteger('order_id')->nullable()->after('plan_id');

            if (!Schema::hasColumn('services','panel_username'))     $t->string('panel_username', 64)->nullable()->after('order_id');
            if (!Schema::hasColumn('services','panel_password_enc')) $t->text('panel_password_enc')->nullable()->after('panel_username');

            if (!Schema::hasColumn('services','enduser_url'))        $t->string('enduser_url', 255)->nullable()->after('panel_password_enc');
            if (!Schema::hasColumn('services','domain'))             $t->string('domain', 191)->nullable()->after('enduser_url');

            if (!Schema::hasColumn('services','server_ip'))          $t->string('server_ip', 64)->nullable()->after('domain');
            if (!Schema::hasColumn('services','nameserver1'))        $t->string('nameserver1', 191)->nullable()->after('server_ip');
            if (!Schema::hasColumn('services','nameserver2'))        $t->string('nameserver2', 191)->nullable()->after('nameserver1');

            if (!Schema::hasColumn('services','plan_slug'))          $t->string('plan_slug', 64)->nullable()->after('nameserver2');
            if (!Schema::hasColumn('services','status'))             $t->string('status', 32)->default('requested')->index()->after('plan_slug');

            if (!Schema::hasColumn('services','last_provisioned_at'))$t->timestamp('last_provisioned_at')->nullable()->after('status');

            // helpful indexes
            if (!Schema::hasColumn('services','panel_username')) {} // (placeholder to avoid IDE warning)
            $t->index(['user_id','status'],'services_user_status_idx');
            $t->index(['order_id'],'services_order_idx');

            // one Service per order (if your data allows it)
            // suppress if duplicates already exist
            try { $t->unique(['order_id'],'services_order_unique'); } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('services')) return;

        Schema::table('services', function (Blueprint $t) {
            // Safely drop added indexes/uniques
            foreach (['services_user_status_idx','services_order_idx','services_order_unique'] as $idx) {
                try { $t->dropIndex($idx); } catch (\Throwable $e) {}
                try { $t->dropUnique($idx); } catch (\Throwable $e) {}
            }

            // Drop columns we added (optional – comment out if you want to keep data)
            foreach ([
                'user_id','plan_id','order_id',
                'panel_username','panel_password_enc',
                'enduser_url','domain','server_ip',
                'nameserver1','nameserver2','plan_slug',
                'status','last_provisioned_at',
            ] as $col) {
                try { $t->dropColumn($col); } catch (\Throwable $e) {}
            }
        });
    }
};
