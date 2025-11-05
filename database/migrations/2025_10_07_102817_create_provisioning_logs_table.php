<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('provisioning_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('service_id')->constrained()->cascadeOnDelete();
            $t->string('step'); // create_user, assign_plan, add_domain, issue_ssl
            $t->json('request')->nullable();
            $t->json('response')->nullable();
            $t->boolean('success')->default(false);
            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('provisioning_logs');
    }
};
