<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Capsule extends Model
{
    // Hangi sütunlara dışarıdan veri eklenebileceğini belirtiyoruz
    protected $fillable = [
        'user_id',
        'message',
        'latitude',
        'longitude'
    ];
}
