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
}
