<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $t) {
            // slug
            if (!Schema::hasColumn('plans', 'slug')) {
                $t->string('slug')->unique();
            }
        });

        Schema::table('plans', function (Blueprint $t) {
            // price_tzs
            if (!Schema::hasColumn('plans', 'price_tzs')) {
                $t->unsignedBigInteger('price_tzs')->default(0);
            }
            // features
            if (!Schema::hasColumn('plans', 'features')) {
                $t->json('features')->nullable();
            }
            // is_active
            if (!Schema::hasColumn('plans', 'is_active')) {
                $t->boolean('is_active')->default(true);
            }
        });

        // jaribu kuweka unique index kwa slug kama haikuwekwa juu (kama ilikuwepo itapita tu)
        try {
            Schema::table('plans', function (Blueprint $t) {
                $t->unique('slug');
            });
        } catch (\Throwable $e) {
            // ignore kama index tayari ipo
        }
    }

    public function down(): void
    {
        // Hakuna rollback ya kufuta columns hapa ili kulinda data.
        // (ukihitaji, unaweza kuziondoa moja moja)
    }
};
