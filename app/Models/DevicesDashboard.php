<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DevicesDashboard extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'devices_dashboard';
    protected $fillable = [
        'devices_id',
        'temperature',
        'humidity',
        'ammonia',
        'time'
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
