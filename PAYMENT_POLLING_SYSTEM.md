# 💳 Payment Polling System - Badala ya Webhook

## 📖 Maelezo ya Mfumo

Mfumo huu unafanya kazi **BILA kutumia webhook**. Badala yake, unatumia **automatic polling** - yaani system inacheck status ya malipo kila sekunde 5 mpaka payment inaconfirm.

---

## 🔄 Jinsi Inavyofanya Kazi

```
1. User → Pays via M-PESA/Tigo/Airtel
         ↓
2. System → Sends STK Push (ZenoPay)
         ↓
3. User → Redirected to Order Summary Page
         ↓
4. Page → Shows "Checking payment status..."
         ↓
5. JavaScript → Starts automatic polling
         ↓
6. Every 5 seconds:
   - Calls: GET /pay/{order}/status
   - Controller calls: ZenoPayClient->status()
   - ZenoPay API returns: payment status
   - If PAID → Updates Order to "paid"
   - JavaScript detects change → Shows success
         ↓
7. Page → Automatically refreshes → Shows service
```

---

## ✅ Kile Kilichofanywa

### **1. PaymentController - pollStatus() Method**

Ongezwa method mpya:

```php
public function pollStatus(Order $order, ZenoPayClient $zeno)
{
    // Check if already paid/failed
    if (in_array($order->status, ['paid','active','complete'...])) {
        return json(['status'=>'paid', 'is_paid'=>true]);
    }

    // Call ZenoPay API to check status
    $statusResp = $zeno->status($order->gateway_order_id);
    
    // If paid, update order
    if ($zenoStatus === 'paid') {
        $order->status = 'paid';
        $order->save();
        return json(['status'=>'paid', 'is_paid'=>true]);
    }
    
    return json(['status'=>'pending', 'is_paid'=>false]);
}
```

**Route:** `GET /pay/{order}/status`

### **2. Order Summary Page - Auto-Polling**

Ukurasa wa `resources/views/public/order.blade.php` sasa una:

**Features:**
- ✅ Real-time status display
- ✅ Animated spinner wakati pending
- ✅ Color-coded status (Yellow=pending, Green=paid, Red=failed)
- ✅ Auto-refresh after payment confirmed
- ✅ JavaScript polling every 5 seconds
- ✅ Maximum 5 minutes polling (60 checks)

**JavaScript Code:**
```javascript
function checkPaymentStatus() {
    fetch('/pay/{order}/status')
        .then(response => response.json())
        .then(data => {
            if (data.is_paid) {
                // Show success message
                // Stop polling
                // Refresh page
            }
        });
}

// Poll every 5 seconds
setInterval(checkPaymentStatus, 5000);
```

### **3. ZenoPayClient - status() Method**

Tayari ilikuwepo! Method hii inaita ZenoPay API:

```php
public function status(string $gatewayOrderId): array
{
    return Http::withHeaders(['x-api-key' => $this->key()])
        ->get($this->base().'/order-status', ['order_id'=>$gatewayOrderId])
        ->json();
}
```

---

## 🎯 Jinsi Ya Kutumia

### **User Experience:**

1. **User analipa:**
   - Weka namba ya simu
   - Click "Pay Now"
   - Pokea STK push kwenye simu
   - Enter PIN

2. **Baada ya kulipa:**
   - Redirected to Order Summary
   - Inaonyesha "Checking payment status..."
   - Spinner spinning (animated)
   - Message: "⏳ Tunasubiri uthibitisho wa malipo..."

3. **System inafanya:**
   - Every 5 seconds → Checks ZenoPay
   - When payment confirmed:
     - ✅ Status changes to "PAID"
     - 🎉 Green checkmark appears
     - Message: "✅ Malipo yamefanikiwa!"
     - Page refreshes after 3 seconds

4. **User anaona:**
   - Service details
   - Control panel link (if provisioned)
   - Dashboard button

---

## ⚙️ Configuration

### **Polling Settings**

Ukitaka kubadilisha settings, edit `resources/views/public/order.blade.php`:

```javascript
const maxPolls = 60;      // Total checks (60 × 5s = 5 min)
const pollDelay = 5000;   // Milliseconds between checks (5000 = 5s)
```

**Mfano:**
- Check every 3 seconds for 3 minutes: `maxPolls=60, pollDelay=3000`
- Check every 10 seconds for 10 minutes: `maxPolls=60, pollDelay=10000`

### **ZenoPay API Endpoint**

Configuration kwenye `config/services.php`:

```php
'zeno' => [
    'base' => env('ZENO_BASE_URL'),
    'key'  => env('ZENO_API_KEY'),
],
```

**`.env` File:**
```env
ZENO_BASE_URL=https://zenoapi.com/api
ZENO_API_KEY=your_api_key_here
```

---

## 📊 Status Flow

### **Order Status Transitions:**

```
pending → (User pays) → paid → (Provisioning) → active
   ↓
   └─→ (Payment fails) → failed
```

### **Status Meanings:**

| Status | Maana | Color | Action |
|--------|-------|-------|--------|
| `pending` | Inasubiri malipo | Yellow | Keep polling |
| `paid` | Malipo yamefanikiwa | Green | Stop polling, refresh page |
| `active` | Service ipo ready | Green | Show control panel link |
| `failed` | Malipo yameshindwa | Red | Stop polling, show retry |
| `cancelled` | Order imefutwa | Red | Stop polling |

---

## 🔍 Debugging

### **Check if Polling is Working:**

1. **Open Browser Console** (F12)
2. Angalia messages:
   ```
   Payment status: {status: "pending", is_paid: false}
   Payment status: {status: "pending", is_paid: false}
   Payment status: {status: "paid", is_paid: true}  ← Success!
   ```

3. **Check Network Tab:**
   - Should see requests to `/pay/{order}/status` every 5 seconds
   - Response should be JSON with status info

### **Manual Status Check:**

Ukitaka ku-check manually:

```bash
# Browser/Postman
GET https://yoursite.com/pay/123/status

# Response:
{
    "status": "paid",
    "is_paid": true,
    "is_terminal": true,
    "message": "Payment confirmed!",
    "payment_ref": "ABC123XYZ"
}
```

### **Common Issues:**

**1. Polling Not Starting:**
- ✅ Check: Order status ni "pending"?
- ✅ Check: JavaScript console for errors?
- ✅ Check: Route `/pay/{order}/status` iko registered?

**2. Status Not Updating:**
- ✅ Check: ZenoPay API credentials correct?
- ✅ Check: `gateway_order_id` iko saved?
- ✅ Check: ZenoPay endpoint reachable?

**3. Page Not Refreshing:**
- ✅ Check: JavaScript console errors?
- ✅ Check: Timeout settings correct?

---

## 💡 Faida za Polling vs Webhook

### **✅ Polling (Tunachotumia):**
- ✅ Hakuna mahitaji ya webhook URL
- ✅ Works kwenye localhost (development)
- ✅ Real-time feedback kwa user
- ✅ Rahisi ku-debug
- ✅ Hakuna webhook signature verification
- ✅ User anaona progress live

### **⚠️ Webhook (Alternative):**
- ❌ Inahitaji public URL (haiwezi localhost)
- ❌ Inahitaji SSL certificate
- ❌ Inahitaji signature verification
- ❌ User hawezi kuona progress live
- ✅ More reliable for background updates
- ✅ Less server load

---

## 🚀 Next Steps

### **1. Test Payment Flow:**
```bash
# Create test order
1. Go to /plans
2. Select plan
3. Fill checkout
4. Pay via M-PESA
5. Observe order summary page
6. Watch status update automatically
```

### **2. Monitor Logs:**
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Check for:
- ZenoPay API calls
- Order status updates
- Any errors
```

### **3. Customize UI:**
- Edit `resources/views/public/order.blade.php`
- Change colors, messages, timing
- Add more animations
- Customize success/failure messages

---

## 🎨 UI Features

### **Status Colors:**
- **Yellow** (#FEF3C7) - Pending/Waiting
- **Green** (#D1FAE5) - Paid/Success
- **Red** (#FEE2E2) - Failed/Cancelled
- **Gray** (#F3F4F6) - Other

### **Icons:**
- ⏳ Hourglass - Pending
- ✅ Checkmark - Success  
- ❌ X Mark - Failed
- 🔄 Spinner (animated) - Checking

### **Messages:**
- Swahili for local users
- Clear status updates
- Countdown/progress indication
- Next steps guidance

---

## 📝 Admin Manual Override

Admin anaweza ku-override status manually:

### **Via Admin Panel:**
```
1. Admin → Orders → View Order
2. Click "Edit"
3. Change status to "paid"
4. Save
```

### **Via Database:**
```sql
UPDATE orders 
SET status = 'paid', 
    payment_ref = 'MANUAL-123',
    updated_at = NOW()
WHERE id = 123;
```

---

## ✅ Summary

**System Sasa Inafanya:**
1. ✅ Sends STK push to user
2. ✅ Shows order summary with real-time status
3. ✅ Automatically polls ZenoPay every 5 seconds
4. ✅ Updates order status when payment confirmed
5. ✅ Shows success message and refreshes page
6. ✅ Works WITHOUT webhook!

**Faida Kwa User:**
- 🎯 Anaona status live
- ⚡ Instant feedback
- 🎨 Beautiful UI with animations
- 📱 Works on mobile
- 🌐 No need to refresh manually

**Faida Kwa Wewe:**
- 🔧 Easy to debug
- 🚀 Works immediately
- 🏠 Works on localhost
- 💰 No webhook setup needed
- 📊 Real-time status tracking

---

**Mfumo ni READY kutumika!** 🎉

Jaribu kulipa mpaka uone jinsi inavyofanya kazi vizuri!

