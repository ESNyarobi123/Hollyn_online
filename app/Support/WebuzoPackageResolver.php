<?php

namespace App\Support;

use App\Models\Plan;
use Illuminate\Support\Str;

class WebuzoPackageResolver
{
    public static function resolve(?Plan $plan): array
    {
        $map = config('webuzo_packages', []);
        $default = $map['_default'] ?? ['package' => config('services.webuzo.default_package','starter'), 'limits' => []];

        if (!$plan) {
            return $default;
        }

        // 1) Try explicit DB column (ikiwa upo; kama hauna, hii itapita tu)
        try {
            if (property_exists($plan, 'webuzo_package') || isset($plan->webuzo_package)) {
                $pkg = (string) $plan->webuzo_package;
                if (strlen($pkg)) {
                    return [
                        'package' => $pkg,
                        'limits'  => $default['limits'],
                    ];
                }
            }
        } catch (\Throwable $e) { /* ignore if column not exists */ }

        // 2) Try by ID
        $keyById = 'id:' . $plan->id;
        if (isset($map[$keyById])) {
            return $map[$keyById];
        }

        // 3) Try by Name
        if ($plan->name) {
            $keyByName = 'name:' . $plan->name;
            if (isset($map[$keyByName])) {
                return $map[$keyByName];
            }
        }

        // 4) Try by slug (from name)
        if ($plan->name) {
            $slug = Str::slug($plan->name);
            $keyBySlug = 'slug:' . $slug;
            if (isset($map[$keyBySlug])) {
                return $map[$keyBySlug];
            }
        }

        // Fallback
        return $default;
    }
}
