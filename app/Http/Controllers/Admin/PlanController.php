<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::latest('id')->paginate(20);
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required','string','max:150'],
            'description'    => ['nullable','string','max:2000'],
            'price_tzs'      => ['required','integer','min:0'],
            'period_months'  => ['required','integer','min:1','max:60'],
            'is_active'      => ['required','boolean'],
        ]);

        Plan::create($data);

        return redirect()->route('admin.plans.index')->with('ok','Plan created.');
    }

    public function show(Plan $plan)
    {
        return view('admin.plans.show', compact('plan'));
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name'           => ['required','string','max:150'],
            'description'    => ['nullable','string','max:2000'],
            'price_tzs'      => ['required','integer','min:0'],
            'period_months'  => ['required','integer','min:1','max:60'],
            'is_active'      => ['required','boolean'],
        ]);

        $plan->update($data);

        return redirect()->route('admin.plans.index')->with('ok','Plan updated.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return back()->with('ok','Plan deleted.');
    }
}
