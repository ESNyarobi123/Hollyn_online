<?php
// database/migrations/2025_10_09_000001_add_gateway_fields_to_orders.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('orders', function (Blueprint $t) {
      $t->string('gateway_order_id', 64)->nullable()->index();
      $t->string('gateway_provider', 32)->nullable();   // mpesa / tigopesa / airtel
      $t->string('payer_phone', 32)->nullable();
      $t->json('gateway_meta')->nullable();             // raw response/store extras
    });
  }
  public function down(): void {
    Schema::table('orders', function (Blueprint $t) {
      $t->dropColumn(['gateway_order_id','gateway_provider','payer_phone','gateway_meta']);
    });
  }
};
