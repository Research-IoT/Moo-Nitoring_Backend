<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DevicesSensors extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'devices_sensors';
    protected $fillable = [
        'year',
        'month',
        'day',
        'timestamp',
        'temperature',
        'humidity',
        'ammonia'
    ];

    protected $hidden = [
        'updated_at',
        'created_at'
    ];


    public function devices()
    {
        return $this->belongsTo(Devices::class);
    }
}
