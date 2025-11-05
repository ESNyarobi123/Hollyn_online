<div class="mt-4 overflow-x-auto -mx-3 sm:mx-0">
  <table class="min-w-full text-sm">
    <thead>
      <tr class="text-left text-brand-slate">
        <th class="py-2 px-3">#</th>
        <th class="py-2 px-3">Customer</th>
        <th class="py-2 px-3">Plan</th>
        <th class="py-2 px-3">Price</th>
        <th class="py-2 px-3">Status</th>
        <th class="py-2 px-3"></th>
      </tr>
    </thead>
    <tbody>
      @forelse($orders as $o)
        <tr class="border-t">
          <td class="py-2 px-3 font-medium text-brand-ocean">#{{ $o->id }}</td>
          <td class="py-2 px-3">
            <div class="text-brand-ocean">{{ $o->customer_name ?? '—' }}</div>
            <div class="text-xs text-brand-slate">{{ $o->customer_email ?? '—' }}</div>
          </td>
          <td class="py-2 px-3">{{ $o->plan->name ?? '—' }}</td>
          <td class="py-2 px-3">TZS {{ number_format($o->price_tzs ?? 0) }}</td>
          <td class="py-2 px-3">
            @php
              $st = (string)($o->status ?? 'pending');
              $badge = [
                'paid' => 'bg-brand-emerald/15 text-brand-emerald',
                'active' => 'bg-brand-emerald/15 text-brand-emerald',
                'pending' => 'bg-brand-cream text-brand-ocean',
                'failed' => 'bg-red-100 text-red-700',
                'provisioning' => 'bg-brand-gold/20 text-brand-ocean',
                'suspended' => 'bg-orange-100 text-orange-700',
                'cancelled' => 'bg-gray-200 text-gray-700',
              ][$st] ?? 'bg-brand-cream text-brand-ocean';
            @endphp
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge }}">{{ ucfirst($st) }}</span>
          </td>
          <td class="py-2 px-3 text-right space-x-2">
            <a href="{{ route('admin.orders.show', $o) }}" class="underline">Open</a>
            <form action="{{ route('admin.orders.destroy', $o) }}" method="POST" class="inline" onsubmit="return confirm('Delete this order?')">
              @csrf @method('DELETE')
              <button class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="py-6 text-center text-brand-slate">No orders found.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
