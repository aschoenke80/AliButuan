<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'organizer_id',
        'title',
        'description',
        'category',
        'audience',
        'location_name',
        'latitude',
        'longitude',
        'start_datetime',
        'end_datetime',
        'image',
        'status',
        'is_featured',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
        'is_featured'    => 'boolean',
        'latitude'       => 'float',
        'longitude'      => 'float',
    ];

    // Valid categories with emojis
    const CATEGORIES = [
        'Festival' => '🎉',
        'Market'   => '🛍️',
        'Concert'  => '🎤',
        'Sports'   => '🏃',
        'Food'     => '🍔',
        'General'  => '📌',
    ];

    // Get the emoji for this event's category
    public function getCategoryEmojiAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? '📌';
    }

    // The organizer who created this event
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    // Users who saved this event
    public function favorites()
    {
        return $this->hasMany(EventFavorite::class);
    }

    // Check if a given user has saved this event
    public function isFavoritedBy($userId): bool
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }

    // Scope: only approved events
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Scope: featured events
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
