<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capsule extends Model
{
    use HasFactory;

    // unlock_date EKLENDİ!
    protected $fillable = ['user_id', 'message', 'image', 'latitude', 'longitude', 'unlock_date'];

    // Tarih formatında tutulacağını belirtiyoruz
    protected $casts = [
        'unlock_date' => 'date:Y-m-d',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
