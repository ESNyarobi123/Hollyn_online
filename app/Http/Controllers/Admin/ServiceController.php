<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProvisionServiceJob;
use App\Models\ProvisioningLog;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ServiceController extends Controller
{
    /**
     * GET /admin/services/create
     * Create new service form
     */
    public function create()
    {
        $orders = \App\Models\Order::with('user:id,name')->whereIn('status', ['paid','active'])->latest('id')->get();
        $plans = \App\Models\Plan::orderBy('name')->get(['id','name','slug']);
        return view('admin.services.create', compact('orders', 'plans'));
    }

    /**
     * POST /admin/services
     * Store new service
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id'       => ['required', 'exists:orders,id'],
            'plan_slug'      => ['required', 'exists:plans,slug'],
            'domain'         => ['required', 'string', 'max:255'],
            'webuzo_username' => ['nullable', 'string', 'max:60'],
            'enduser_url'    => ['nullable', 'url', 'max:255'],
            'status'         => ['required', 'in:requested,provisioning,active,failed,suspended,cancelled'],
        ]);

        $service = Service::create($data);

        return redirect()->route('admin.services.show', $service)->with('ok', 'Service created successfully.');
    }

    /**
     * GET /admin/services
     * Lists services with filters + KPIs.
     */
    public function index(Request $request)
    {
        $query = Service::query()
            ->with(['order.user:id,name,email', 'plan:id,name'])
            ->when($request->filled('status'), fn ($q) =>
                $q->where('status', $request->string('status')->toString())
            )
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->string('search')->toString();
                $q->where(function ($qq) use ($s) {
                    $qq->where('domain', 'like', "%{$s}%")
                       ->orWhere('panel_username', 'like', "%{$s}%");
                });
            })
            ->latest('id');

        $services = $query->paginate(20)->withQueryString();

        // KPIs (simple counts)
        $stats = [
            'total'        => Service::count(),
            'active'       => Service::where('status', 'active')->count(),
            'provisioning' => Service::where('status', 'provisioning')->count(),
            'suspended'    => Service::where('status', 'suspended')->count(),
        ];

        return view('admin.services.index', compact('services', 'stats'));
    }

    /**
     * GET /admin/services/{service}
     * Details + logs.
     */
    public function show(Service $service)
    {
        $service->load(['order.user:id,name,email', 'plan:id,name']);
        $logs = ProvisioningLog::where('service_id', $service->id)
            ->latest('id')
            ->paginate(15);

        return view('admin.services.show', compact('service', 'logs'));
    }

    /**
     * GET /admin/services/{service}/edit
     * Minimal admin edit (domain/status/username/url).
     */
    public function edit(Service $service)
    {
        $service->load(['order.user:id,name,email', 'plan:id,name']);
        return view('admin.services.edit', compact('service'));
    }

    /**
     * PUT/PATCH /admin/services/{service}
     * Update a few admin fields safely.
     */
    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'domain'         => ['nullable', 'string', 'max:255'],
            'panel_username' => ['nullable', 'string', 'max:60'],
            'enduser_url'    => ['nullable', 'url', 'max:255'],
            'status'         => ['required', 'in:provisioning,active,suspended,cancelled'],
        ]);

        $service->fill($data)->save();

        return redirect()
            ->route('admin.services.show', $service)
            ->with('ok', 'Service updated.');
    }

    /**
     * DELETE /admin/services/{service}
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('ok', 'Service deleted.');
    }

    /**
     * POST /admin/services/{service}/reprovision
     * Queue reprovision via job.
     */
    public function reprovision(Service $service)
    {
        $service->status = 'provisioning';
        $service->save();

        ProvisionServiceJob::dispatch($service->id);

        return back()->with('ok', 'Re-provision queued.');
    }

    /**
     * POST /admin/services/{service}/send-credentials
     * Sends a simple email with panel URL + username (no password).
     * Replace with a proper Mailable when ready.
     */
    public function sendCredentials(Service $service)
    {
        $panel = $service->enduser_url ?: config('services.webuzo.enduser_url');
        $user  = optional($service->order)->user;

        if (!$user) {
            return back()->withErrors('No user attached to this service.');
        }

        try {
            Mail::raw(
                "Hello {$user->name},\n\nYour hosting service is ready.\n\nPanel: {$panel}\nUsername: {$service->panel_username}\n\nFor security, we do not email passwords. If you need help logging in, contact support.",
                function ($m) use ($user) {
                    $m->to($user->email)->subject('Your hosting control panel details');
                }
            );

            return back()->with('ok', 'Credentials email sent.');
        } catch (\Throwable $e) {
            return back()->withErrors('Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * POST /admin/services/{service}/suspend
     */
    public function suspend(Service $service)
    {
        $service->status = 'suspended';
        $service->save();

        return back()->with('ok', 'Service suspended.');
    }

    /**
     * POST /admin/services/{service}/activate
     */
    public function activate(Service $service)
    {
        $service->status = 'active';
        $service->save();

        return back()->with('ok', 'Service activated.');
    }
}
