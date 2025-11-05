<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentWebhookController;

Route::post('/webhooks/zenopay', [PaymentWebhookController::class,'handle']);
