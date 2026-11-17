<?php

namespace App\Models;

use App\Support\TicketCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EventRegistration extends Model
{
    /** @use HasFactory<\Database\Factories\EventRegistrationFactory> */
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'email',
        'phone',
        'ticket_code',
        'ticket_token',
    ];

    protected $hidden = [
        'ticket_token',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $registration): void {
            $registration->ticket_code ??= TicketCode::generate();
            $registration->ticket_token ??= TicketCode::token();
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function attendance(): HasOne
    {
        return $this->hasOne(EventAttendance::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term): void {
            $query
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('ticket_code', 'like', "%{$term}%");
        });
    }

    public function isCheckedIn(): bool
    {
        return $this->attendance()->exists();
    }

    public function checkIn(?User $user = null): EventAttendance
    {
        return $this->attendance()->firstOrCreate(
            [],
            [
                'checked_in_at' => now(),
                'checked_in_by' => $user?->getKey(),
            ],
        );
    }
}
