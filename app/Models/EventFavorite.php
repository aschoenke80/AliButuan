<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventFavorite extends Model
{
    public $timestamps = false; // Only has created_at

    protected $fillable = ['user_id', 'event_id'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
