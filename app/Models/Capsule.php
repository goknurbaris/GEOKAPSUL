<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capsule extends Model
{
    use HasFactory;

    // pin_code EKLENDİ!
   protected $fillable = ['user_id', 'message', 'image', 'audio', 'latitude', 'longitude', 'unlock_date', 'pin_code'];
    protected $casts = [
        'unlock_date' => 'date:Y-m-d',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
