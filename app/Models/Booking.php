<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'event_name',
        'location',
        'advertise_start',
        'advertise_end',
        'time_start',
        'time_end',
        'hours',
        'days',
        'total_hours',
        'total_cost',
        'contact_name',
        'contact_email',
        'contact_phone',
        'notes',
        'status',
    ];

    protected $casts = [
        'advertise_start' => 'date',
        'advertise_end'   => 'date',
    ];

    const RATE_PER_HOUR = 100; // Philippine Peso

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function computeCost(int $days, int $hours): array
    {
        $totalHours = ($days * 24) + $hours;
        $totalCost  = $totalHours * self::RATE_PER_HOUR;
        return compact('totalHours', 'totalCost');
    }
}
