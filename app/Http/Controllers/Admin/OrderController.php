<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['user','plan'])
            ->when($request->status, fn($q)=>$q->where('status',$request->status))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get(['id','name']);
        $plans = Plan::orderBy('name')->get(['id','name']);
        return view('admin.orders.create', compact('users','plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'        => ['required','exists:users,id'],
            'plan_id'        => ['required','exists:plans,id'],
            'customer_name'  => ['required','string','max:255'],
            'customer_email' => ['required','email','max:255'],
            'customer_phone' => ['required','string','max:30'],
            'domain'         => ['nullable','string','max:255'],
            'price_tzs'      => ['required','integer','min:0'],
            'currency'       => ['nullable','string','max:10'],
            'status'         => ['required','in:pending,paid,failed,active,cancelled,complete,succeeded'],
            'payment_ref'    => ['nullable','string','max:255'],
            'gateway_provider' => ['nullable','string','max:100'],
        ]);

        // Auto-fill missing data from user
        $user = User::find($data['user_id']);
        if (!isset($data['currency'])) {
            $data['currency'] = 'TZS';
        }
        if (!isset($data['order_uuid'])) {
            $data['order_uuid'] = (string) \Illuminate\Support\Str::uuid();
        }
        
        $order = Order::create($data);

        return redirect()->route('admin.orders.show', $order)->with('ok','Order created successfully.');
    }

    public function show(Order $order)
    {
        $order->load(['user','plan']);
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['user','plan']);
        $users = User::orderBy('name')->get(['id','name']);
        $plans = Plan::orderBy('name')->get(['id','name']);
        return view('admin.orders.edit', compact('order','users','plans'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'user_id'        => ['required','exists:users,id'],
            'plan_id'        => ['required','exists:plans,id'],
            'customer_name'  => ['required','string','max:255'],
            'customer_email' => ['required','email','max:255'],
            'customer_phone' => ['required','string','max:30'],
            'domain'         => ['nullable','string','max:255'],
            'price_tzs'      => ['required','integer','min:0'],
            'currency'       => ['nullable','string','max:10'],
            'status'         => ['required','in:pending,paid,failed,active,cancelled,complete,succeeded'],
            'payment_ref'    => ['nullable','string','max:255'],
            'gateway_provider' => ['nullable','string','max:100'],
        ]);

        $order->update($data);

        return redirect()->route('admin.orders.show', $order)->with('ok','Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('ok','Order deleted.');
    }

    /**
     * Check payment status from ZenoPay and update order
     */
    public function checkStatus(Order $order, \App\Services\ZenoPayClient $zeno)
    {
        if (!$order->gateway_order_id) {
            return back()->withErrors('Order has no gateway_order_id to check.');
        }

        try {
            $statusResp = $zeno->status($order->gateway_order_id);
            
            // Extract status from ZenoPay response
            $zenoStatus = strtolower((string) (\Illuminate\Support\Arr::get($statusResp, 'state') ?? \Illuminate\Support\Arr::get($statusResp, 'status', 'pending')));
            
            // Update order based on ZenoPay status
            if (in_array($zenoStatus, ['paid', 'success', 'completed', 'active'], true)) {
                $order->status = 'paid';
                $order->payment_ref = \Illuminate\Support\Arr::get($statusResp, 'transaction_id') ?? \Illuminate\Support\Arr::get($statusResp, 'reference') ?? $order->payment_ref;
                $order->gateway_meta = $this->mergeGatewayMeta($order->gateway_meta, ['admin_check' => $statusResp, 'checked_at' => now()]);
                $order->save();
                
                return back()->with('ok', '✅ Payment confirmed! Order marked as PAID.');
                
            } elseif (in_array($zenoStatus, ['failed', 'cancelled', 'expired'], true)) {
                $order->status = 'failed';
                $order->gateway_meta = $this->mergeGatewayMeta($order->gateway_meta, ['admin_check' => $statusResp, 'checked_at' => now()]);
                $order->save();
                
                return back()->with('ok', '❌ Payment FAILED according to ZenoPay.');
                
            } else {
                return back()->with('ok', '⏳ Payment still PENDING. Status: ' . $zenoStatus);
            }
            
        } catch (\Throwable $e) {
            return back()->withErrors('Error checking status: ' . $e->getMessage());
        }
    }

    /**
     * Manually mark order as paid (admin override)
     */
    public function markPaid(Order $order)
    {
        if ($order->status === 'paid') {
            return back()->with('ok', 'Order is already marked as PAID.');
        }

        $order->status = 'paid';
        $order->payment_ref = $order->payment_ref ?? 'ADMIN-' . strtoupper(\Illuminate\Support\Str::random(8));
        $order->gateway_meta = $this->mergeGatewayMeta($order->gateway_meta, [
            'manual_override' => true,
            'marked_by' => \Illuminate\Support\Facades\Auth::id(),
            'marked_at' => now(),
        ]);
        $order->save();

        return back()->with('ok', '✅ Order manually marked as PAID!');
    }

    /**
     * Helper to merge gateway meta
     */
    protected function mergeGatewayMeta($current, array $new): array
    {
        $curr = is_array($current) ? $current : (is_string($current) ? json_decode($current, true) : []);
        if (!is_array($curr)) $curr = [];
        return array_merge($curr, $new);
    }
}
