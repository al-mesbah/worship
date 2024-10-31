<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Reservation extends Model
{
    protected $guarded = [];
    protected $appends = [
        'date',
        'updated_at'
    ];
    protected $casts = [
        'is_active' => 'boolean'
    ];

    protected function date(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes) {
                return Jalalian::fromCarbon(Carbon::parse($attributes['reserved_at']))->format('Y/m/d');
            },
            set: fn($value) => $value,
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes) {
                return Jalalian::fromCarbon(Carbon::parse($attributes['updated_at']))->format('Y/m/d');
            },
            set: fn($value) => $value,
        );
    }
}
