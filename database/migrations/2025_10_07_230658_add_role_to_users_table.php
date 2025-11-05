<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users', 'role')) {
                $t->string('role', 16)->default('user')->after('email'); // 'user' | 'admin'
                $t->index('role');
            }
            // (optional) phone field kama haujaweka
            if (!Schema::hasColumn('users', 'phone')) {
                $t->string('phone', 32)->nullable()->after('password');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $t) {
                $t->dropIndex(['role']);
                $t->dropColumn('role');
            });
        }
        if (Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $t) {
                $t->dropColumn('phone');
            });
        }
    }
};
