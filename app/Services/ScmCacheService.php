<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ScmCacheService
{
    public static function invalidateGlobalData(): int
    {
        foreach (self::globalKeys() as $key) {
            Cache::forget($key);
        }

        return (int) Cache::increment('scm:data-version');
    }

    private static function globalKeys(): array
    {
        return [
            'dashboard:port-count',
            'dashboard:port-markers',
            'dashboard:economic-trend',
            'dashboard:risk-trend',
            'dashboard:currency-trend',
            'scm:dashboard:port-count',
            'scm:dashboard:port-markers',
            'scm:dashboard:economic-trend',
            'scm:dashboard:risk-trend',
            'scm:dashboard:currency-trend',
        ];
    }
}
