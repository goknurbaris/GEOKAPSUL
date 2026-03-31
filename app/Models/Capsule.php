<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Capsule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'message', 'image', 'audio',
        'latitude', 'longitude', 'unlock_date',
        'pin_code', 'share_code'
    ];

    protected $casts = [
        'unlock_date' => 'date:Y-m-d',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Kullanıcı ilişkisi
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Benzersiz paylaşım kodu oluştur
     */
    public function generateShareCode(): string
    {
        do {
            $code = Str::random(12);
        } while (static::where('share_code', $code)->exists());

        $this->share_code = $code;
        $this->save();

        return $code;
    }

    /**
     * Paylaşım URL'i al
     */
    public function getShareUrlAttribute(): ?string
    {
        return $this->share_code ? route('capsule.shared', $this->share_code) : null;
    }

    /**
     * PIN korumalı mı?
     */
    public function getHasPinAttribute(): bool
    {
        return !empty($this->pin_code);
    }

    /**
     * Tarih kilitli mi?
     */
    public function getIsTimeLockedAttribute(): bool
    {
        if (!$this->unlock_date) return false;
        return now()->lt($this->unlock_date);
    }

    /**
     * Belirtilen konumdan mesafeyi hesapla (metre cinsinden)
     */
    public function distanceFrom(float $lat, float $lng): float
    {
        $earthRadius = 6371000; // metre

        $latFrom = deg2rad($this->latitude);
        $lngFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lngTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $a = sin($latDelta / 2) ** 2 +
             cos($latFrom) * cos($latTo) * sin($lngDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Scope: Sayfalama için
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Arama
     */
    public function scopeSearch($query, ?string $search)
    {
        if (!$search) return $query;

        return $query->where('message', 'like', "%{$search}%");
    }
}
