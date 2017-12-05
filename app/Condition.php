<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    protected $fillable = [
        'site_id', 'high_humidity', 'low_humidity', 'high_moisture', 'low_moisture', 'high_lux', 'low_lux', 'high_temp', 'low_temp', 'winter_high_temp', 'winter_low_temp'
    ];
}
