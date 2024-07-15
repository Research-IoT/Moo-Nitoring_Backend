<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Notifications extends Model
{
    use HasFactory, HasApiTokens;

    protected   $table = 'notifications';

    protected $fillable = [
        'tittle',
        'description',
        'date',
        'time'
    ];

    protected $hidden = [
        'updated_at',
        'created_at'
    ];

}
