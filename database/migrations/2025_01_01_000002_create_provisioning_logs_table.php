<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('provisioning_logs')) {
            Schema::create('provisioning_logs', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('service_id')->index();
                $t->string('step', 64)->index();
                $t->longText('request')->nullable();
                $t->longText('response')->nullable();
                $t->boolean('success')->default(false)->index();
                $t->timestamps();

                // fk (optional, silent if services lacks PK or cross-db)
                // try/catch to avoid breaking if no FK support
                try {
                    $t->foreign('service_id')->references('id')->on('services')->cascadeOnDelete();
                } catch (\Throwable $e) {}
            });
        }
    }

    public function down(): void
    {
        try { Schema::dropIfExists('provisioning_logs'); } catch (\Throwable $e) {}
    }
};
