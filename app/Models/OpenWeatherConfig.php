<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenWeatherConfig extends Model
{
    use HasFactory;

    protected $table = 'open_weather_configs';

    protected $fillable = [
        'api_key'
    ];

    protected $hidden = [
        'updated_at',
        'created_at'
    ];
}
