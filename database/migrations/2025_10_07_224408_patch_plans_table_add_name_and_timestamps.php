<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add NAME kama haipo
        if (!Schema::hasColumn('plans', 'name')) {
            Schema::table('plans', function (Blueprint $t) {
                $t->string('name')->default('')->after('id');
            });
        }

        // Ensure SLUG ipo na unique (kama tulishai-add, hii itapuuzwa)
        if (!Schema::hasColumn('plans', 'slug')) {
            Schema::table('plans', function (Blueprint $t) {
                $t->string('slug')->unique()->after('name');
            });
        } else {
            try {
                Schema::table('plans', function (Blueprint $t) {
                    $t->unique('slug');
                });
            } catch (\Throwable $e) {
                // ignore kama index tayari ipo
            }
        }

        // PRICE / FEATURES / IS_ACTIVE tuliongeza awali—hapa ni safety tu
        if (!Schema::hasColumn('plans', 'price_tzs')) {
            Schema::table('plans', function (Blueprint $t) {
                $t->unsignedBigInteger('price_tzs')->default(0);
            });
        }
        if (!Schema::hasColumn('plans', 'features')) {
            Schema::table('plans', function (Blueprint $t) {
                $t->json('features')->nullable();
            });
        }
        if (!Schema::hasColumn('plans', 'is_active')) {
            Schema::table('plans', function (Blueprint $t) {
                $t->boolean('is_active')->default(true);
            });
        }

        // Timestamps (created_at, updated_at)
        $hasCreated = Schema::hasColumn('plans', 'created_at');
        $hasUpdated = Schema::hasColumn('plans', 'updated_at');

        if (!$hasCreated && !$hasUpdated) {
            Schema::table('plans', function (Blueprint $t) {
                $t->timestamps();
            });
        } else {
            if (!$hasCreated) {
                Schema::table('plans', function (Blueprint $t) {
                    $t->timestamp('created_at')->nullable();
                });
            }
            if (!$hasUpdated) {
                Schema::table('plans', function (Blueprint $t) {
                    $t->timestamp('updated_at')->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        // hatufuti data kwenye rollback kwa usalama
    }
};
