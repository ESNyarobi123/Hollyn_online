<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * App\Models\Order
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property int|null                        $plan_id
 * @property string|null                     $order_uuid
 * @property string|null                     $customer_name
 * @property string|null                     $customer_email
 * @property string|null                     $customer_phone
 * @property string|null                     $payer_phone
 * @property string|null                     $domain
 * @property int                             $price_tzs
 * @property string                          $currency
 * @property string                          $status
 * @property string|null                     $payment_ref
 * @property string|null                     $gateway_order_id
 * @property string|null                     $gateway_provider
 * @property array|null                      $gateway_meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read string                     $price_formatted
 * @property-read bool                       $is_paid
 * @property-read bool                       $is_terminal
 * @property-read string                     $status_label
 */
class Order extends Model
{
    use HasFactory;

    // =======================
    // Status constants/sets
    // =======================
    public const STATUS_PENDING   = 'pending';
    public const STATUS_PAID      = 'paid';
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_COMPLETE  = 'complete';
    public const STATUS_SUCCEEDED = 'succeeded';
    public const STATUS_FAILED    = 'failed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_CANCELED  = 'canceled';

    /** @var string[] */
    public const PAID_SET = [
        self::STATUS_PAID, self::STATUS_ACTIVE, self::STATUS_COMPLETE, self::STATUS_SUCCEEDED,
    ];

    /** @var string[] */
    public const FAILED_SET = [
        self::STATUS_FAILED, self::STATUS_CANCELLED, self::STATUS_CANCELED,
    ];

    /** @var string[] */
    public const TERMINAL_SET = [
        self::STATUS_FAILED, self::STATUS_CANCELLED, self::STATUS_CANCELED, self::STATUS_COMPLETE, self::STATUS_SUCCEEDED,
    ];

    /** @var array<int,string> - Allow all fields for admin control */
    protected $fillable = [
        'user_id','plan_id','order_uuid','customer_name','customer_email',
        'customer_phone','payer_phone','domain','price_tzs','currency','status','payment_ref',
        // subscription duration
        'duration_months','base_price_monthly','discount_percentage','notes',
        // idempotency/gateway
        'gateway_order_id','gateway_provider','gateway_meta',
    ];
    
    // Empty guarded allows mass assignment of all fields
    protected $guarded = [];

    /** @var array<string,string> */
    protected $casts = [
        'price_tzs'            => 'integer',
        'base_price_monthly'   => 'integer',
        'duration_months'      => 'integer',
        'discount_percentage'  => 'decimal:2',
        'gateway_meta'         => 'array',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
    ];

    /** @var array<int,string> */
    protected $appends = [
        'price_formatted',
        'is_paid',
        'is_terminal',
        'status_label',
    ];

    // =======================
    // Relations
    // =======================
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function service()
    {
        return $this->hasOne(Service::class);
    }

    public function events()
    {
        return $this->hasMany(PaymentEvent::class);
    }

    // =======================
    // Scopes
    // =======================
    public function scopePaid($q)
    {
        return $q->whereIn('status', self::PAID_SET);
    }

    public function scopeFailed($q)
    {
        return $q->whereIn('status', self::FAILED_SET);
    }

    public function scopeStatus($q, string|array $status)
    {
        $s = array_map(fn ($x) => strtolower((string) $x), (array) $status);
        return $q->whereIn('status', $s);
    }

    public function scopeForUser($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }

    public function scopeRecent($q, int $limit = 10)
    {
        return $q->latest('id')->limit($limit);
    }

    // =======================
    // Accessors / Mutators
    // =======================
    /** Always show formatted price like "TZS 12,345" */
    protected function priceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => 'TZS ' . number_format((int) $this->price_tzs)
        );
    }

    /** Normalize status to lowercase on set */
    protected function status(): Attribute
    {
        return Attribute::make(
            set: fn ($v) => is_string($v) ? strtolower(trim($v)) : $v
        );
    }

    /** Normalize currency (default TZS) */
    protected function currency(): Attribute
    {
        return Attribute::make(
            set: fn ($v) => strtoupper($v ?: 'TZS'),
            get: fn ($v) => strtoupper($v ?: 'TZS'),
        );
    }

    /** Sanitize domain (remove scheme + trailing slash) */
    protected function domain(): Attribute
    {
        return Attribute::make(
            set: function ($v) {
                if (!is_string($v) || $v === '') return $v;
                $d = strtolower(trim($v));
                $d = preg_replace('#^https?://#', '', $d);
                return rtrim($d, '/');
            }
        );
    }

    /** Normalize phone(s) → 2557xxxxxxxx (both customer_phone and payer_phone) */
    protected function customerPhone(): Attribute
    {
        return Attribute::make(
            set: fn ($v) => $this->normalizeMsisdn($v)
        );
    }

    protected function payerPhone(): Attribute
    {
        return Attribute::make(
            set: fn ($v) => $this->normalizeMsisdn($v)
        );
    }

    /** Computed flags */
    protected function isPaid(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->status, self::PAID_SET, true)
        );
    }

    protected function isTerminal(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->status, self::TERMINAL_SET, true)
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    self::STATUS_PENDING   => 'Pending',
                    self::STATUS_PAID      => 'Paid',
                    self::STATUS_ACTIVE    => 'Active',
                    self::STATUS_COMPLETE  => 'Complete',
                    self::STATUS_SUCCEEDED => 'Succeeded',
                    self::STATUS_FAILED    => 'Failed',
                    self::STATUS_CANCELLED => 'Cancelled',
                    self::STATUS_CANCELED  => 'Cancelled',
                ];
                return $labels[$this->status] ?? ucfirst((string) $this->status);
            }
        );
    }

    // =======================
    // Helpers (idempotency/status)
    // =======================

    /**
     * Hakikisha tuna gateway_order_id ya kudumu kwa order hii.
     * @param  bool $persist  Ukiwa true ita-save mara moja
     * @return string         gateway_order_id
     */
    public function ensureGatewayOrderId(bool $persist = true): string
    {
        if (empty($this->gateway_order_id)) {
            $this->gateway_order_id = 'ORD-'.Str::ulid();
            if ($persist) $this->save();
        }
        return $this->gateway_order_id;
    }

    /**
     * Badilisha status + optionally set payment_ref & merge gateway_meta.
     */
    public function setStatus(string $status, ?string $paymentRef = null, array $meta = []): self
    {
        $this->status = strtolower($status);
        if ($paymentRef !== null) $this->payment_ref = $paymentRef;
        if (!empty($meta)) $this->gateway_meta = $this->mergeArray($this->gateway_meta, $meta);
        $this->save();
        return $this;
    }

    public function markPending(array $meta = []): self
    {
        return $this->setStatus(self::STATUS_PENDING, null, $meta);
    }

    public function markPaid(?string $ref = null, array $meta = []): self
    {
        return $this->setStatus(self::STATUS_PAID, $ref, $meta);
    }

    public function markComplete(array $meta = []): self
    {
        return $this->setStatus(self::STATUS_COMPLETE, null, $meta);
    }

    public function markFailed(?string $ref = null, array $meta = []): self
    {
        return $this->setStatus(self::STATUS_FAILED, $ref, $meta);
    }

    public function markCancelled(?string $ref = null, array $meta = []): self
    {
        return $this->setStatus(self::STATUS_CANCELLED, $ref, $meta);
    }

    // =======================
    // Pricing Utilities
    // =======================
    
    /**
     * Calculate discount percentage based on duration
     */
    public static function calculateDiscountForDuration(int $months): float
    {
        if ($months >= 12) return 20.0;  // 20% off for 12+ months
        if ($months >= 6)  return 15.0;  // 15% off for 6-11 months
        if ($months >= 3)  return 10.0;  // 10% off for 3-5 months
        return 0.0;                       // No discount for 1-2 months
    }
    
    /**
     * Calculate total price with discount applied
     */
    public static function calculateTotalPrice(int $baseMonthly, int $months): int
    {
        $discount = self::calculateDiscountForDuration($months);
        $subtotal = $baseMonthly * $months;
        $discountAmount = $subtotal * ($discount / 100);
        return (int) round($subtotal - $discountAmount);
    }
    
    /**
     * Get formatted duration text (e.g., "3 months", "1 year")
     */
    public function getDurationFormatted(): string
    {
        $months = $this->duration_months ?? 1;
        if ($months === 12) return '1 year';
        if ($months % 12 === 0) return ($months / 12) . ' years';
        if ($months === 1) return '1 month';
        return $months . ' months';
    }
    
    /**
     * Get savings amount (if any)
     */
    public function getSavingsAmount(): int
    {
        if (!$this->base_price_monthly || !$this->duration_months) return 0;
        $fullPrice = $this->base_price_monthly * $this->duration_months;
        return $fullPrice - $this->price_tzs;
    }

    // =======================
    // Utilities
    // =======================
    protected function normalizeMsisdn(?string $raw): ?string
    {
        if (!$raw) return null;
        $raw = preg_replace('/\D+/', '', $raw); // keep digits only
        // 07xxxxxxxx -> 2557xxxxxxxx
        if (preg_match('/^0([67]\d{8})$/', $raw, $m)) {
            return '255'.$m[1];
        }
        // +2557xxxxxxxx or 2557xxxxxxxx
        if (preg_match('/^(?:\+?255)([67]\d{8})$/', $raw, $m)) {
            return '255'.$m[1];
        }
        // leave as-is if it doesn't match known TZ mobile patterns
        return $raw;
    }

    protected function mergeArray($current, array $new): array
    {
        $curr = is_array($current) ? $current : (is_string($current) ? json_decode($current, true) : []);
        if (!is_array($curr)) $curr = [];
        return array_replace_recursive($curr, $new);
    }

    // =======================
    // Route key (optional)
    // =======================
    // Ukiamua kutumia {order:order_uuid} kwenye routes, uncomment hii:
    // public function getRouteKeyName(): string
    // {
    //     return 'order_uuid';
    // }
}
