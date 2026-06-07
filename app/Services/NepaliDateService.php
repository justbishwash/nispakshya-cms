<?php

namespace App\Services;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;

class NepaliDateService
{
    /**
     * Convert an English date string (Y-m-d) to Nepali date strings.
     *
     * Returns:
     *   'np'    => Devanagari script  e.g. "२०८२ जेठ २४"
     *   'np_en' => English digits     e.g. "2082 Jestha 24"
     */
    public static function convert(string $date): array
    {
        try {
            $nd = LaravelNepaliDate::from($date);
            return [
                'np'    => $nd->toNepaliDate('np'),
                'np_en' => $nd->toNepaliDate('en'),
            ];
        } catch (\Throwable $e) {
            return ['np' => null, 'np_en' => null];
        }
    }

    /**
     * Get today's Nepali date.
     */
    public static function today(): array
    {
        return static::convert(now()->toDateString());
    }
}
