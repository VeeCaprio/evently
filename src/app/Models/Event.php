<?php

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'event_category_id',
        'title',
        'slug',
        'description',
        'banner',
        'organizer',
        'start_at',
        'end_at',
        'location',
        'quota',
        'ticket_price',
        'status',
        'is_featured',
    ];

    protected $appends = [
        'banner_url',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'quota' => 'integer',
            'ticket_price' => 'decimal:2',
            'status' => EventStatus::class,
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function attendances(): HasManyThrough
    {
        return $this->hasManyThrough(EventAttendance::class, EventRegistration::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', EventStatus::Published->value);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->whereIn('status', [
            EventStatus::Published->value,
            EventStatus::Completed->value,
        ]);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_at', '>=', now());
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where('end_at', '<', now());
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term): void {
            $query
                ->where('title', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%")
                ->orWhere('organizer', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopeInCategory(Builder $query, ?string $categorySlug): Builder
    {
        if (! $categorySlug) {
            return $query;
        }

        return $query->whereHas('category', fn (Builder $query) => $query->where('slug', $categorySlug));
    }

    public function registeredCount(): int
    {
        return (int) ($this->registrations_count ?? $this->registrations()->count());
    }

    public function remainingQuota(): int
    {
        return max($this->quota - $this->registeredCount(), 0);
    }

    public function isSoldOut(): bool
    {
        return $this->remainingQuota() === 0;
    }

    public function isUpcoming(): bool
    {
        return $this->start_at->isFuture();
    }

    public function isOngoing(): bool
    {
        return $this->start_at->isPast() && $this->end_at->isFuture();
    }

    public function hasEnded(): bool
    {
        return $this->end_at->isPast();
    }

    public function isRegistrationOpen(): bool
    {
        return $this->status === EventStatus::Published
            && $this->isUpcoming()
            && ! $this->isSoldOut();
    }

    public function getBannerUrlAttribute(): ?string
    {
        if (! $this->banner) {
            return null;
        }

        if (Str::startsWith($this->banner, ['http://', 'https://'])) {
            return $this->banner;
        }

        return Storage::disk('public')->url($this->banner);
    }
}
