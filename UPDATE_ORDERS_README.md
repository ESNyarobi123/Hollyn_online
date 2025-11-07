# 🔧 Update Pending Orders - Quick Guide

## Current Pending Orders:

1. **Order #33**
   - Customer: Salehe
   - Amount: TZS 12,000
   - Created: Oct 11, 2025

2. **Order #34**
   - Customer: Salehe  
   - Amount: TZS 25,000
   - Created: Oct 11, 2025

---

## ✅ How to Fix

### Option 1: Admin Panel (RECOMMENDED)

**If Payment Was Successful:**
```
1. Go to: https://hosting.hollyn.online/admin/orders/33
2. Click: "✅ Mark as Paid" (green button)
3. Repeat for order #34
```

**If Payment Failed/Expired:**
```
1. Go to: https://hosting.hollyn.online/admin/orders/33/edit
2. Change Status to: "failed"
3. Click Save
4. Repeat for order #34
```

---

### Option 2: Command Line (QUICK)

**Mark as PAID (if payment confirmed):**
```bash
php artisan tinker
```
```php
$order = \App\Models\Order::find(33);
$order->status = 'paid';
$order->payment_ref = 'MANUAL-'.strtoupper(\Illuminate\Support\Str::random(8));
$order->save();
echo "✅ Order #33 marked as PAID\n";

$order = \App\Models\Order::find(34);
$order->status = 'paid';
$order->payment_ref = 'MANUAL-'.strtoupper(\Illuminate\Support\Str::random(8));
$order->save();
echo "✅ Order #34 marked as PAID\n";
```

**Or Mark as FAILED (if expired):**
```php
\App\Models\Order::whereIn('id', [33, 34])->update(['status' => 'failed']);
echo "✅ Orders marked as FAILED\n";
```

---

### Option 3: Direct SQL (FASTEST)

**Open database and run:**

**Mark as PAID:**
```sql
UPDATE orders 
SET status = 'paid', 
    payment_ref = 'MANUAL-ADMIN',
    updated_at = NOW()
WHERE id IN (33, 34);
```

**Or Mark as FAILED:**
```sql
UPDATE orders 
SET status = 'failed',
    updated_at = NOW()
WHERE id IN (33, 34);
```

---

## ⚠️ Important Notes

1. **Check Payment First!**
   - Verify with customer if they actually paid
   - Check M-PESA/bank statements
   - Only mark as "paid" if confirmed

2. **Old Orders**
   - These orders are from October (30+ days old)
   - ZenoPay API doesn't have them anymore (404)
   - Safe to mark as "failed" if no payment proof

3. **Future Orders**
   - New orders will auto-update via polling
   - Manual check button available
   - Admin quick actions work for all orders

---

## 🎯 My Recommendation

**For Orders #33 & #34:**

Since they're **30+ days old** and **ZenoPay doesn't have records**:

1. **Contact customer** (Salehe - ezekielsalehe00@gmail.com)
2. **Ask if payment was made**
3. If YES → Mark as PAID via admin panel
4. If NO → Mark as FAILED

**Most likely:** These are expired/abandoned orders → Mark as FAILED

---

## 📞 Contact Customer Script

```
Subject: Order Confirmation - Hollyn Hosting

Hi Salehe,

We have 2 pending orders in our system:
- Order #33: TZS 12,000
- Order #34: TZS 25,000

Can you confirm if you completed payment for these orders?
If yes, please send us the M-PESA transaction code.

If you didn't pay, no worries - you can place a new order anytime!

Best regards,
Hollyn Hosting Team
```

---

## ✅ After Fixing

Once you update the orders:

1. Run check script:
```bash
php check_orders.php
```

2. Verify in admin panel:
```
https://hosting.hollyn.online/admin/orders
```

3. For future orders:
   - Auto-polling will work
   - Manual check button available
   - Admin quick actions ready

---

**All tools are in place for future orders to update automatically!** 🎉




