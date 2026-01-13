<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SystemConfig
{
    /**
     * Get a setting value, checking .env first, then DB, then default.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        // 1. Check if an environment override exists (e.g. IMMICH_URL)
        $envValue = env(strtoupper($key));
        if ($envValue !== null) {
            return $envValue;
        }

        // 2. Check Database (Cached)
        $dbValue = Cache::rememberForever('setting_' . $key, function () use ($key) {
            return Setting::where('key', $key)->value('value');
        });

        if ($dbValue !== null) {
            return $dbValue;
        }

        return $default;
    }

    /**
     * Set a value in the database and clear cache.
     */
    public static function set(string $key, $value)
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget('setting_' . $key);
    }
}
