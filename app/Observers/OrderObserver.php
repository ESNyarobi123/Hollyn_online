<?php

namespace App\Observers;

use App\Jobs\ProvisionWebuzoAccount;
use App\Models\Order;

class OrderObserver
{
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            $new = $order->status;
            if (in_array($new, ['paid','complete'])) {
                // kama hakuna service active/provisioning, start job
                $order->loadMissing('service');
                if (!$order->service || !in_array($order->service->status, ['provisioning','active'])) {
                    ProvisionWebuzoAccount::dispatch($order->id);
                }
            }
        }
    }
}
