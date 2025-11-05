<?php
// database/migrations/xxxx_add_provision_fields.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('services', function (Blueprint $t) {
      if (!Schema::hasColumn('services','webuzo_username')) $t->string('webuzo_username')->nullable();
      if (!Schema::hasColumn('services','webuzo_temp_password_enc')) $t->text('webuzo_temp_password_enc')->nullable();
      if (!Schema::hasColumn('services','enduser_url')) $t->string('enduser_url')->nullable();
      if (!Schema::hasColumn('services','status')) $t->string('status')->default('requested');
      $t->unique('order_id'); // one service per order
    });
  }
  public function down(): void {
    Schema::table('services', function (Blueprint $t) {
      $t->dropUnique(['order_id']);
    });
  }
};
