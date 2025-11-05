<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * @property int|null    $id
 * @property int|null    $order_id
 * @property int|null    $service_id
 * @property int|null    $user_id
 * @property string|null $status
 * @property string|null $step
 * @property string|null $message
 * @property array|null  $payload
 * @property array|null  $meta
 * @property string|null $error_code
 * @property int|null    $attempt
 * @property string|null $queue
 * @property string|null $job_id
 * @property string|null $worker
 * @property string|null $host
 * @property string|null $correlation_id
 * @property string|null $provider
 * @property string|null $environment
 * @property Carbon|null $queued_at
 * @property Carbon|null $started_at
 * @property Carbon|null $finished_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read bool        $is_terminal
 * @property-read int|null    $duration_seconds
 * @property-read string|null $status_label
 */
class ProvisioningLog extends Model
{
    use HasFactory;

    /** @var string */
    protected $table = 'provisioning_logs';

    // ---- Status constants ----
    public const STATUS_REQUESTED     = 'requested';
    public const STATUS_QUEUED        = 'queued';
    public const STATUS_STARTED       = 'started';
    public const STATUS_PROVISIONING  = 'provisioning';
    public const STATUS_SUCCESS       = 'success';
    public const STATUS_FAILED        = 'failed';
    public const STATUS_CANCELLED     = 'cancelled';

    /** @var string[] statuses considered terminal */
    public const TERMINAL_STATUSES = [
        self::STATUS_SUCCESS,
        self::STATUS_FAILED,
        self::STATUS_CANCELLED,
    ];

    /** @var array<int,string> */
    protected $fillable = [
        'order_id',
        'service_id',
        'user_id',
        'status',
        'step',
        'message',
        'payload',
        'meta',
        'error_code',
        'attempt',
        'queue',
        'job_id',
        'worker',
        'host',
        'correlation_id',
        'provider',
        'environment',
        'queued_at',
        'started_at',
        'finished_at',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'payload'      => 'array',
        'meta'         => 'array',
        'attempt'      => 'integer',
        'queued_at'    => 'datetime',
        'started_at'   => 'datetime',
        'finished_at'  => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /** @var array<int,string> */
    protected $appends = [
        'is_terminal',
        'duration_seconds',
        'status_label',
    ];

    // -------------------------------------------------
    // Boot: default values (defensive against schema)
    // -------------------------------------------------
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            // Default status
            if (empty($m->status)) {
                $m->status = self::STATUS_REQUESTED;
            }
            // Default attempt
            if ($m->attempt === null) {
                $m->attempt = 0;
            }
            // correlation_id if column exists
            if (Schema::hasColumn($m->getTable(), 'correlation_id') && empty($m->correlation_id)) {
                $m->correlation_id = (string) Str::ulid();
            }
            // provider default if column exists (e.g., 'webuzo')
            if (Schema::hasColumn($m->getTable(), 'provider') && empty($m->provider)) {
                $m->provider = 'webuzo';
            }
            // env default if column exists
            if (Schema::hasColumn($m->getTable(), 'environment') && empty($m->environment)) {
                $m->environment = app()->environment();
            }
        });
    }

    // -------------------------------------------------
    // Relations
    // -------------------------------------------------
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // -------------------------------------------------
    // Scopes
    // -------------------------------------------------
    public function scopeForOrder($q, int $orderId)
    {
        return $q->where('order_id', $orderId);
    }

    public function scopeForService($q, int $serviceId)
    {
        return $q->where('service_id', $serviceId);
    }

    public function scopeForUser($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }

    public function scopeRecent($q, int $limit = 50)
    {
        return $q->latest('id')->limit($limit);
    }

    public function scopeStatus($q, string|array $status)
    {
        $statuses = (array) $status;
        return $q->whereIn('status', array_map('strtolower', $statuses));
    }

    public function scopeActive($q)
    {
        return $q->whereNotIn('status', self::TERMINAL_STATUSES);
    }

    public function scopeFailed($q)
    {
        return $q->where('status', self::STATUS_FAILED);
    }

    // -------------------------------------------------
    // Accessors (Appends)
    // -------------------------------------------------
    public function getIsTerminalAttribute(): bool
    {
        return in_array($this->status, self::TERMINAL_STATUSES, true);
    }

    public function getDurationSecondsAttribute(): ?int
    {
        $start = $this->started_at ?? $this->queued_at ?? null;
        $end   = $this->finished_at ?? now();
        if (!$start) return null;
        return $end->diffInSeconds($start);
    }

    public function getStatusLabelAttribute(): ?string
    {
        $labels = [
            self::STATUS_REQUESTED    => 'Requested',
            self::STATUS_QUEUED       => 'Queued',
            self::STATUS_STARTED      => 'Started',
            self::STATUS_PROVISIONING => 'Provisioning',
            self::STATUS_SUCCESS      => 'Success',
            self::STATUS_FAILED       => 'Failed',
            self::STATUS_CANCELLED    => 'Cancelled',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    // -------------------------------------------------
    // Helpers / Lifecycle setters (fluent)
    // -------------------------------------------------
    public function setStatus(string $status, ?string $message = null, array $meta = []): self
    {
        $this->status  = strtolower($status);
        if ($message !== null) $this->message = $message;
        if (!empty($meta)) $this->meta = $this->mergeArray($this->meta, $meta);

        // timestamps for common transitions
        if ($this->status === self::STATUS_QUEUED && !$this->queued_at) {
            $this->queued_at = now();
        }
        if (in_array($this->status, [self::STATUS_STARTED, self::STATUS_PROVISIONING], true) && !$this->started_at) {
            $this->started_at = now();
        }
        if (in_array($this->status, self::TERMINAL_STATUSES, true) && !$this->finished_at) {
            $this->finished_at = now();
        }
        return $this;
    }

    public function markQueued(?string $queue = null, ?string $jobId = null, ?string $worker = null, ?string $host = null): self
    {
        $this->queue     = $queue ?? $this->queue;
        $this->job_id    = $jobId ?? $this->job_id;
        $this->worker    = $worker ?? $this->worker;
        $this->host      = $host ?? $this->host;
        $this->queued_at = $this->queued_at ?: now();
        return $this->setStatus(self::STATUS_QUEUED);
    }

    public function markStarted(?string $worker = null, ?string $host = null, ?string $step = 'init'): self
    {
        $this->worker    = $worker ?? $this->worker;
        $this->host      = $host ?? $this->host;
        $this->started_at= $this->started_at ?: now();
        $this->step      = $step ?? $this->step;
        return $this->setStatus(self::STATUS_STARTED);
    }

    public function markProgress(string $step, ?string $message = null, array $meta = []): self
    {
        $this->step = $step;
        return $this->setStatus(self::STATUS_PROVISIONING, $message, $meta);
    }

    public function markSucceeded(?string $message = 'Provisioning completed', array $meta = []): self
    {
        $this->finished_at = $this->finished_at ?: now();
        return $this->setStatus(self::STATUS_SUCCESS, $message, $meta);
    }

    public function markFailed(string $message, ?string $code = null, array $meta = []): self
    {
        $this->error_code  = $code;
        $this->finished_at = $this->finished_at ?: now();
        return $this->setStatus(self::STATUS_FAILED, $message, $meta);
    }

    // -------------------------------------------------
    // Utilities
    // -------------------------------------------------
    protected function mergeArray($current, array $new): array
    {
        $curr = is_array($current) ? $current : (is_string($current) ? json_decode($current, true) : []);
        if (!is_array($curr)) $curr = [];
        return array_replace_recursive($curr, $new);
    }

    // Quick factory style creator
    public static function startFor(?Order $order, ?Service $service, array $attrs = []): self
    {
        /** @var self $log */
        $log = new self();
        $log->order_id   = $order?->id;
        $log->service_id = $service?->id;
        $log->user_id    = $order?->user_id ?? $service?->user_id;
        $log->attempt    = Arr::get($attrs, 'attempt', 0);
        $log->payload    = Arr::get($attrs, 'payload', []);
        $log->meta       = Arr::get($attrs, 'meta', []);
        $log->message    = Arr::get($attrs, 'message', 'Provision requested');
        $log->queue      = Arr::get($attrs, 'queue');
        $log->job_id     = Arr::get($attrs, 'job_id');
        $log->worker     = Arr::get($attrs, 'worker');
        $log->host       = Arr::get($attrs, 'host');

        $log->setStatus(self::STATUS_REQUESTED);
        $log->save();

        return $log;
    }
}
