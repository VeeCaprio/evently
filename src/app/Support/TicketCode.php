<?php

namespace App\Support;

use App\Models\EventRegistration;
use Illuminate\Support\Str;

final class TicketCode
{
    /**
     * Characters that are easy to read out loud and safe to type.
     */
    private const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public static function generate(string $prefix = 'EVL'): string
    {
        do {
            $code = $prefix . '-' . self::segment() . '-' . self::segment();
        } while (EventRegistration::query()->where('ticket_code', $code)->exists());

        return $code;
    }

    public static function token(): string
    {
        return Str::random(64);
    }

    private static function segment(int $length = 4): string
    {
        $segment = '';

        for ($i = 0; $i < $length; $i++) {
            $segment .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
        }

        return $segment;
    }
}
