<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Route;

class Service extends Model
{
    use HasFactory;

    // =======================
    // Status constants
    // =======================
    public const STATUS_REQUESTED     = 'requested';
    public const STATUS_PROVISIONING  = 'provisioning';
    public const STATUS_ACTIVE        = 'active';
    public const STATUS_FAILED        = 'failed';
    public const STATUS_SUSPENDED     = 'suspended';
    public const STATUS_CANCELLED     = 'cancelled';

    /** @var string[] */
    public const ACTIVE_STATES = [self::STATUS_ACTIVE];
    /** @var string[] */
    public const PROVISION_STATES = [self::STATUS_PROVISIONING, self::STATUS_REQUESTED, 'queued', 'pending', 'pending_provision'];
    /** @var string[] */
    public const FAILED_STATES = [self::STATUS_FAILED, self::STATUS_CANCELLED];

    protected $fillable = [
        'order_id',
        'plan_slug',
        'domain',
        'webuzo_username',
        'webuzo_temp_password_enc',
        'enduser_url',
        'status',
        // 'user_id', // kama meza yako ina hii col, unaweza kufungua hii pia
    ];

    protected $hidden = [
        'webuzo_temp_password_enc',
    ];

    protected $casts = [
        // Laravel 12: encrypted cast hutumia APP_KEY — hifadhi salama
        'webuzo_temp_password_enc' => 'encrypted',
        'status' => 'string',
    ];

    protected $appends = [
        'is_active',
        'status_label',
        'panel_href',
    ];

    // =======================
    // Relations
    // =======================
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function logs()
    {
        return $this->hasMany(ProvisioningLog::class);
    }

    /** Plan kwa kutumia slug kama ulivyo-design schema */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_slug', 'slug');
    }

    // =======================
    // Scopes
    // =======================
    public function scopeActive($q)
    {
        return $q->whereIn('status', self::ACTIVE_STATES);
    }

    public function scopeProvisioning($q)
    {
        return $q->whereIn('status', self::PROVISION_STATES);
    }

    public function scopeFailed($q)
    {
        return $q->whereIn('status', self::FAILED_STATES);
    }

    public function scopeStatus($q, string|array $status)
    {
        $s = array_map(fn($x)=>strtolower((string)$x), (array)$status);
        return $q->whereIn('status', $s);
    }

    /**
     * Scope services of a given user id.
     * In case services table haina user_id, tunascope kupitia order->user_id.
     */
    public function scopeForUser($q, int $userId)
    {
        if (schema_has_column($this->getTable(), 'user_id')) {
            return $q->where('user_id', $userId);
        }
        return $q->whereHas('order', fn($o)=>$o->where('user_id', $userId));
    }

    // Helper to check schema column simply
    protected function schemaHas(string $col): bool
    {
        return schema_has_column($this->getTable(), $col);
    }

    // =======================
    // Accessors / Mutators
    // =======================
    /** Lowercase + trim status on set */
    protected function status(): Attribute
    {
        return Attribute::make(
            set: fn ($v) => is_string($v) ? strtolower(trim($v)) : $v
        );
    }

    /** Sanitize domain: lowercase, toa scheme & trailing slash */
    protected function domain(): Attribute
    {
        return Attribute::make(
            set: function ($v) {
                if (!is_string($v) || $v === '') return $v;
                $d = strtolower(trim($v));
                $d = preg_replace('#^https?://#', '', $d); // remove http(s)://
                $d = rtrim($d, "/");
                return $d;
            }
        );
    }

    /** Force username: lowercase, [a-z0-9_], length clamp 3..32 */
    protected function webuzoUsername(): Attribute
    {
        return Attribute::make(
            set: function ($v) {
                if (!is_string($v)) return $v;
                $u = strtolower($v);
                $u = preg_replace('/[^a-z0-9_]/', '', $u) ?: $u;
                $u = substr($u, 0, 32);
                if (strlen($u) < 3) $u = str_pad($u, 3, '0'); // basic padding
                return $u;
            }
        );
    }

    /** Computed: je service ni active? */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->status, self::ACTIVE_STATES, true)
        );
    }

    /** Computed: human label ya status */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    self::STATUS_REQUESTED    => 'Requested',
                    self::STATUS_PROVISIONING => 'Provisioning',
                    self::STATUS_ACTIVE       => 'Active',
                    self::STATUS_FAILED       => 'Failed',
                    self::STATUS_SUSPENDED    => 'Suspended',
                    self::STATUS_CANCELLED    => 'Cancelled',
                ];
                return $labels[$this->status] ?? ucfirst((string)$this->status);
            }
        );
    }

    /**
     * Computed: href ya kufungua panel.
     * Chagua SSO route kama ipo; vinginevyo tumia enduser_url.
     */
    protected function panelHref(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (Route::has('me.panel')) {
                    return route('me.panel');
                }
                return $this->enduser_url ?: null;
            }
        );
    }

    // =======================
    // Helpers
    // =======================

    /**
     * Badilisha status ya service; optionally andika log ukitumia ProvisioningLog.
     */
    public function setStatus(string $status, ?string $message = null, array $meta = []): self
    {
        $this->status = strtolower($status);
        $this->save();

        // Optional log (silent fail kama model/tabla haipo)
        try {
            if (class_exists(\App\Models\ProvisioningLog::class)) {
                $this->logs()->create([
                    'user_id'   => $this->order->user_id ?? null,
                    'status'    => $this->status,
                    'message'   => $message,
                    'meta'      => $meta,
                    'service_id'=> $this->id,
                    'order_id'  => $this->order_id,
                ]);
            }
        } catch (\Throwable $e) {
            // usivunje flow ya app kwa failure ya logging
        }

        return $this;
    }

    public function markProvisioning(string $message = 'Provisioning started', array $meta = []): self
    {
        return $this->setStatus(self::STATUS_PROVISIONING, $message, $meta);
    }

    public function markActive(string $message = 'Service activated', array $meta = []): self
    {
        return $this->setStatus(self::STATUS_ACTIVE, $message, $meta);
    }

    public function markFailed(string $message, array $meta = []): self
    {
        return $this->setStatus(self::STATUS_FAILED, $message, $meta);
    }
}

/**
 * Helper ya ku-check column bila ku-import Schema kila mahali.
 * Unaweza kuihamishia kwenye helper file kama ukipenda.
 */
if (!function_exists('schema_has_column')) {
    function schema_has_column(string $table, string $col): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn($table, $col);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
