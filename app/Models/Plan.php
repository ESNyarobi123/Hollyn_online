<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

/**
 * App\Models\Plan
 *
 * @property int                              $id
 * @property string                           $name
 * @property string                           $slug
 * @property int                              $price_tzs
 * @property array|null                       $features
 * @property bool                             $is_active
 * @property \Illuminate\Support\Carbon|null  $created_at
 * @property \Illuminate\Support\Carbon|null  $updated_at
 *
 * @property-read string                      $price_formatted
 */
class Plan extends Model
{
    use HasFactory;

    /** @var string[] */
    protected $fillable = [
        'name',
        'slug',
        'price_tzs',
        'features',
        'is_active',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'price_tzs' => 'integer',
        'features'  => 'array',
        'is_active' => 'boolean',
    ];

    // =======================
    // Relations
    // =======================
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    // =======================
    // Scopes
    // =======================
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered($q)
    {
        // bei ndogo kwanza
        return $q->orderBy('price_tzs');
    }

    public function scopeSearch($q, ?string $term)
    {
        if (!$term) return $q;
        $t = trim($term);
        return $q->where(function ($qq) use ($t) {
            $qq->where('name', 'like', "%{$t}%")
               ->orWhere('slug', 'like', "%{$t}%");
        });
    }

    /** Shortcut ya common listing kwa UI */
    public function scopeForDisplay($q, ?string $term = null)
    {
        return $q->active()->search($term)->ordered();
    }

    // =======================
    // Accessors & Mutators (modern Attribute API)
    // =======================

    /** Hakikisha `features` daima inarudi array hata kama ni null */
    protected function features(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?: [],
        );
    }

    /** Price formatted (TZS 12,345) */
    protected function priceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => 'TZS ' . number_format((int) $this->price_tzs)
        );
    }

    /** Sanitize name (trim spaces) */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => is_string($value) ? trim($value) : $value
        );
    }

    /** Sanitize + normalize slug kama ukituma moja kwa moja */
    protected function slug(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                $val = is_string($value) ? trim($value) : $value;
                return $val ? Str::slug($val) : $val;
            }
        );
    }

    /** Hakikisha bei ni integer isiyo-chini ya sifuri */
    protected function priceTzs(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => max(0, (int) $value)
        );
    }

    // =======================
    // Helpers
    // =======================

    /**
     * Rudisha thamani ya feature kwa key (ina-support dot notation),
     * au $default ukikosa.
     */
    public function featureValue(string $key, $default = '—'): string
    {
        $val = data_get($this->features ?? [], $key, $default);
        return is_scalar($val) ? (string) $val : json_encode($val);
    }

    /**
     * Tafuta plan kwa slug au id moja kwa moja (rahisi kwa route-model binding za mikono).
     */
    public static function findBySlugOrId(string|int $idOrSlug): ?self
    {
        if (is_numeric($idOrSlug)) {
            return static::query()->find((int) $idOrSlug);
        }
        return static::query()->where('slug', (string) $idOrSlug)->first();
    }

    // =======================
    // Slugging & Model Events
    // =======================

    protected static function booted(): void
    {
        // Auto-set slug kama haipo wakati wa kuunda
        static::creating(function (Plan $plan) {
            if (empty($plan->slug) && !empty($plan->name)) {
                $plan->slug = static::uniqueSlugFrom($plan->name);
            }
        });

        // Hakikisha tuna slug sahihi pia wakati wa ku-save (e.g. name imebadilika)
        static::saving(function (Plan $plan) {
            if (empty($plan->slug) && !empty($plan->name)) {
                $plan->slug = static::uniqueSlugFrom($plan->name, $plan->id);
            }
        });
    }

    /**
     * Tengeneza slug unique (basic). Ikiwa slug inapatikana, ongeza -1, -2, ...
     */
    protected static function uniqueSlugFrom(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base ?: Str::random(6);

        $i = 1;
        while (static::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '<>', $ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    // =======================
    // Route Key (optional)
    // =======================
    // Ukiamua kutumia {plan:slug} kwenye routes, uncomment hii:
    // public function getRouteKeyName(): string
    // {
    //     return 'slug';
    // }
}
