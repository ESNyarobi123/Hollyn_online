<div class="mt-4 overflow-x-auto -mx-3 sm:mx-0">
  <table class="min-w-full text-sm">
    <thead>
    <tr class="text-left text-brand-slate">
      <th class="py-2 px-3">#</th>
      <th class="py-2 px-3">User</th>
      <th class="py-2 px-3">Plan</th>
      <th class="py-2 px-3">Domain</th>
      <th class="py-2 px-3">Status</th>
      <th class="py-2 px-3"></th>
    </tr>
    </thead>
    <tbody>
    @forelse($services as $s)
      <tr class="border-t">
        <td class="py-2 px-3 font-medium text-brand-ocean">#{{ $s->id }}</td>
        <td class="py-2 px-3">
          <div class="text-brand-ocean">{{ $s->order->customer_name ?? '—' }}</div>
          <div class="text-xs text-brand-slate">{{ $s->order->customer_email ?? '—' }}</div>
        </td>
        <td class="py-2 px-3">{{ $s->order->plan->name ?? '—' }}</td>
        <td class="py-2 px-3">{{ $s->order->domain ?? '—' }}</td>
        <td class="py-2 px-3">
          @php
            $st = (string)($s->status ?? 'pending_provision');
            $badge = [
              'active' => 'bg-brand-emerald/15 text-brand-emerald',
              'pending_provision' => 'bg-brand-cream text-brand-ocean',
              'suspended' => 'bg-orange-100 text-orange-700',
            ][$st] ?? 'bg-brand-cream text-brand-ocean';
          @endphp
          <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge }}">{{ str($st)->replace('_',' ')->title() }}</span>
        </td>
        <td class="py-2 px-3 text-right space-x-2">
          <a href="{{ route('admin.services.show', $s) }}" class="underline">Open</a>
          <form action="{{ route('admin.services.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('Delete this service?')">
            @csrf @method('DELETE')
            <button class="text-red-600 hover:underline">Delete</button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="6" class="py-6 text-center text-brand-slate">No services found.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
