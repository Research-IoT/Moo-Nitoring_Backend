<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Devices extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'devices';
    protected $fillable = [
        'name',
        'automatic',
        'heater',
        'blower'
    ];

    protected $hidden = [
        'updated_at',
        'created_at'
    ];

    protected $casts = [
        'automatic' =>'boolean',
        'heater' =>'boolean',
        'blower' =>'boolean'
    ];


    public function sensor()
    {
        return $this->hasMany(DevicesSensors::class);
    }
}
