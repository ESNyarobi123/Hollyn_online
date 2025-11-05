<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    // LANDING
    public function home()
    {
        $plans = Plan::where('is_active', true)->orderBy('price_tzs')->take(4)->get();
        return view('public.home', compact('plans'));
    }

    public function plans()
    {
        $plans = Plan::where('is_active', true)->orderBy('price_tzs')->get();
        return view('public.plans', compact('plans'));
    }

    public function show(Plan $plan)
    {
        abort_unless($plan->is_active, 404);
        return view('public.checkout', compact('plan'));
    }

    public function createOrder(Request $r)
    {
        $data = $r->validate([
            'plan_id'        => ['required','exists:plans,id'],
            'customer_name'  => ['required','string','max:120'],
            'customer_email' => ['required','email'],
            'customer_phone' => ['required','string','max:30'],
            'domain'         => ['nullable','string','max:191'],
        ]);

        $plan = Plan::whereKey($data['plan_id'])
            ->where('is_active', true)
            ->firstOrFail();

        // Normalize → local display (07/06xxxxxxxx)
        $localPhone = $this->normalizeTzPhone($data['customer_phone']);
        if (!$localPhone) {
            return back()->withErrors([
                'customer_phone' => 'Namba sio sahihi. Tumia 07xxxxxxxx / 06xxxxxxxx au +2557xxxxxxxx / 2557xxxxxxxx / 002557xxxxxxxx.',
            ])->withInput();
        }

        // Pia tengeneza payer_phone (E.164 bila +) → 2556/2557xxxxxxxx
        $payerMsisdn = $this->toMsisdn255($localPhone); // guaranteed by normalizeTzPhone()

        $userId = Auth::id();

        // Zuia duplicate pending ndani ya dakika 5 (kwa user + plan)
        $existing = Order::where('user_id', $userId)
            ->where('plan_id', $plan->id)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(5))
            ->first();

        if ($existing) {
            return redirect()->route('pay.start', ['order' => $existing->id]);
        }

        $order = DB::transaction(function () use ($userId, $plan, $data, $localPhone, $payerMsisdn) {
            return Order::create([
                'user_id'        => $userId,
                'plan_id'        => $plan->id,
                'order_uuid'     => (string) Str::uuid(),
                'customer_name'  => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $localPhone,     // 07/06xxxxxxxx (kwa display na mawasiliano)
                'payer_phone'    => $payerMsisdn,    // 2556/2557xxxxxxxx (kwa malipo)
                'domain'         => $data['domain'] ? strtolower(trim($data['domain'])) : null,
                'price_tzs'      => $plan->price_tzs,
                'currency'       => env('APP_CURRENCY','TZS'),
                'status'         => 'pending',
            ]);
        });

        return redirect()->route('pay.start', ['order' => $order->id]);
    }

    public function summary(Order $order)
    {
        if (Auth::check() && $order->user_id && $order->user_id !== Auth::id()) {
            abort(403);
        }
        $order->loadMissing(['plan','service']);
        return view('public.order', compact('order'));
    }

    /**
     * Normalize TZ mobile (accepts: 07/06xxxxxxxx, +2557/6xxxxxxxx, 2557/6xxxxxxxx, 002557/6xxxxxxxx).
     * Returns local "07/06xxxxxxxx" for consistent display/storage in customer_phone.
     */
    protected function normalizeTzPhone(string $raw): ?string
    {
        $trimmed = trim($raw);

        // +2557/6xxxxxxxx
        if (preg_match('/^\+255([6-7]\d{8})$/', $trimmed, $m)) {
            return '0' . $m[1];
        }

        // Remove spaces/dashes etc.
        $digits = preg_replace('/\D+/', '', $trimmed);
        if (!$digits) return null;

        // 07/06xxxxxxxx
        if (preg_match('/^0([6-7]\d{8})$/', $digits, $m)) {
            return '0' . $m[1];
        }

        // 2557/6xxxxxxxx
        if (preg_match('/^255([6-7]\d{8})$/', $digits, $m)) {
            return '0' . $m[1];
        }

        // 002557/6xxxxxxxx
        if (preg_match('/^00255([6-7]\d{8})$/', $digits, $m)) {
            return '0' . $m[1];
        }

        return null;
    }

    /**
     * Convert local "07/06xxxxxxxx" -> "2557/2556xxxxxxxx" (E.164 minus '+').
     * Assumes valid local number.
     */
    protected function toMsisdn255(string $local07): string
    {
        // $local07 lazima iwe 07/06xxxxxxxx
        return '255' . substr($local07, 1);
    }
}
