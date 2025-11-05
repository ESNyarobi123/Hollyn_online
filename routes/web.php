<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

// Public / Checkout
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;

// User
use App\Http\Controllers\User\DashboardController as UserDash;
use App\Http\Controllers\User\ServiceProvisionController;

// Admin
use App\Http\Controllers\Admin\AdminDashboardController as AdminDash;
use App\Http\Controllers\Admin\PlanController as AdminPlan;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\OrderController as AdminOrder;
use App\Http\Controllers\Admin\ServiceController as AdminService;

// Middleware
use App\Http\Middleware\IsAdmin;

/*
|--------------------------------------------------------------------------
| Public (Landing + Plans + Checkout)
|--------------------------------------------------------------------------
*/
Route::get('/', [CheckoutController::class, 'home'])->name('home');

Route::get('/plans', [CheckoutController::class, 'plans'])->name('plans');

Route::get('/checkout/{plan}', [CheckoutController::class, 'show'])
    ->whereNumber('plan')
    ->name('checkout.show');

Route::post('/checkout', [CheckoutController::class, 'createOrder'])
    ->name('checkout.create');

Route::get('/order/{order}', [CheckoutController::class, 'summary'])
    ->whereNumber('order')
    ->name('order.summary');

/*
|--------------------------------------------------------------------------
| Payments
|--------------------------------------------------------------------------
*/
Route::get('/pay', fn () => redirect()->route('plans'))
    ->name('pay.redirect');

Route::get('/pay/{order}', [PaymentController::class, 'start'])
    ->whereNumber('order')
    ->name('pay.start');

/* live status polling for order (used by summary page) */
Route::get('/pay/{order}/status', [PaymentController::class, 'pollStatus'])
    ->whereNumber('order')
    ->name('pay.status');

/* webhook (closure placeholder): log + 200 OK
   NOTE: ongeza exception ya CSRF kwa route hii kwenye VerifyCsrfToken */
Route::post('/webhooks/zeno', function (Request $request) {
    Log::info('Zeno webhook hit', [
        'ip'   => $request->ip(),
        'body' => $request->all(),
        'ua'   => $request->userAgent(),
    ]);
    // TODO: hapa baadaye tumia PaymentController@webhook na uthibitishe signature
    return response()->json(['ok' => true]);
})->name('webhooks.zeno');

/*
|--------------------------------------------------------------------------
| Auth (Breeze / Fortify / Jetstream)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| User (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function (): void {
    // Primary user dashboard
    Route::get('/dashboard', [UserDash::class, 'index'])->name('dashboard');

    // Compat alias for older navs using me.dashboard
    Route::get('/me/dashboard', [UserDash::class, 'index'])->name('me.dashboard');

    /**
     * Open Panel (SSO via Webuzo enduser :2003)
     * - Requests SSO for customer's username
     * - Accepts JSON or plain URL body
     */
    Route::get('/me/panel', function () {
        /** @var \App\Models\User $u */
        $u = Auth::user();

        $svc = \App\Models\Service::whereHas('order', fn ($q) => $q->where('user_id', $u->id))
            ->where('status', 'active')
            ->latest('id')
            ->first();

        if (!$svc) {
            return redirect()->route('dashboard')->withErrors('No active hosting found.');
        }

        // username may be saved as webuzo_username OR panel_username
        $username = $svc->webuzo_username ?? $svc->panel_username ?? null;
        if (!$username) {
            return redirect()->route('dashboard')->withErrors('Active hosting missing panel username.');
        }

        // Build SSO endpoint (Webuzo Enduser :2003)
        $base = rtrim((string) config('services.webuzo.enduser_url'), '/'); // e.g. https://example.com:2003
        if (empty($base)) {
            return redirect()->route('dashboard')->withErrors('Webuzo Enduser URL not configured.');
        }
        $ssoUrl = $base . '/index.php?api=json&act=sso&loginAs=' . rawurlencode($username);

        $req = \Illuminate\Support\Facades\Http::asForm()
            ->withBasicAuth(
                (string) config('services.webuzo.admin_user'),
                (string) config('services.webuzo.admin_pass')
            )
            ->when(!config('services.webuzo.verify_ssl', true), fn ($r) => $r->withoutVerifying());

        $resp = $req->post($ssoUrl, ['noip' => 1]);

        if (!$resp->successful()) {
            return redirect()->route('dashboard')->withErrors('Failed to get SSO link (HTTP ' . $resp->status() . ').');
        }

        $target = null;
        try {
            $json = $resp->json() ?? [];
            $target = $json['done']['url'] ?? null;
        } catch (\Throwable $e) {
            // ignore JSON errors
        }

        if (!$target) {
            $body = trim((string) $resp->body());
            if (str_starts_with($body, 'http')) $target = $body;
        }

        if (!$target) {
            return redirect()->route('dashboard')->withErrors('SSO response invalid (no URL).');
        }

        return redirect()->away($target);
    })->name('me.panel');

    // Self-service: trigger provisioning for a PAID order (explicit id)
    Route::post('/me/services/provision/{order}', [ServiceProvisionController::class, 'provision'])
        ->whereNumber('order')
        ->name('me.services.provision');

    // Finish setup — auto-pick latest PAID order without an attached service
    Route::post('/me/services/provision-latest', [ServiceProvisionController::class, 'provisionLatest'])
        ->name('me.services.provisionLatest');

    // Lightweight status polling for dashboard (JSON)
    Route::get('/me/services/status', [ServiceProvisionController::class, 'status'])
        ->name('me.services.status');

    // Placeholder aliases for blades using these names (redirect to dashboard)
    Route::get('/me/services', fn () => redirect()->route('dashboard'))
        ->name('me.services.index');

    Route::get('/me/orders', fn () => redirect()->route('dashboard'))
        ->name('me.orders.index');
});

/*
|--------------------------------------------------------------------------
| Admin (Authenticated + IsAdmin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', IsAdmin::class])
    ->prefix('admin')
    ->as('admin.')
    ->group(function (): void {
        // Dashboard
        Route::get('/', [AdminDash::class, 'index'])->name('index');

        // Back-compat alias
        Route::get('/home', fn () => redirect()->route('admin.index'))->name('home');

        // Plans: full CRUD
        Route::resource('plans', AdminPlan::class)
            ->parameters(['plans' => 'plan'])
            ->where(['plan' => '[0-9]+']);

        // Users: full CRUD
        Route::resource('users', AdminUser::class)
            ->parameters(['users' => 'user'])
            ->where(['user' => '[0-9]+']);
        
        // User impersonation
        Route::post('users/{user}/impersonate', [AdminUser::class, 'impersonate'])
            ->whereNumber('user')
            ->name('users.impersonate');
        
        Route::post('users/stop-impersonating', [AdminUser::class, 'stopImpersonating'])
            ->name('users.stopImpersonating');
        
        // View user credentials
        Route::get('users/{user}/credentials', [AdminUser::class, 'credentials'])
            ->whereNumber('user')
            ->name('users.credentials');

        // Orders: full CRUD
        Route::resource('orders', AdminOrder::class)
            ->parameters(['orders' => 'order'])
            ->where(['order' => '[0-9]+']);

        // Services: limited CRUD (no create/store)
        Route::resource('services', AdminService::class)
            ->only(['index', 'show', 'edit', 'update', 'destroy'])
            ->parameters(['services' => 'service'])
            ->where(['service' => '[0-9]+']);

        // Extra service actions
        Route::post('services/{service}/reprovision', [AdminService::class, 'reprovision'])
            ->whereNumber('service')
            ->name('services.reprovision');

        Route::post('services/{service}/send-credentials', [AdminService::class, 'sendCredentials'])
            ->whereNumber('service')
            ->name('services.sendCredentials');

        Route::post('services/{service}/suspend', [AdminService::class, 'suspend'])
            ->whereNumber('service')
            ->name('services.suspend');

        Route::post('services/{service}/activate', [AdminService::class, 'activate'])
            ->whereNumber('service')
            ->name('services.activate');
    });

// Optional fallback (uncomment to enable)
// Route::fallback(fn () => abort(404));
