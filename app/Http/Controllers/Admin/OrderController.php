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
            'user_id'   => ['required','exists:users,id'],
            'plan_id'   => ['required','exists:plans,id'],
            'price_tzs' => ['required','integer','min:0'],
            'status'    => ['required','in:pending,paid,failed,active,cancelled'],
            'notes'     => ['nullable','string','max:2000'],
        ]);

        $order = Order::create($data);

        return redirect()->route('admin.orders.edit', $order)->with('ok','Order created.');
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
            'user_id'   => ['required','exists:users,id'],
            'plan_id'   => ['required','exists:plans,id'],
            'price_tzs' => ['required','integer','min:0'],
            'status'    => ['required','in:pending,paid,failed,active,cancelled'],
            'notes'     => ['nullable','string','max:2000'],
        ]);

        $order->update($data);

        return redirect()->route('admin.orders.edit', $order)->with('ok','Order updated.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('ok','Order deleted.');
    }
}
