<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Capsule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'message', 'image', 'audio',
        'latitude', 'longitude', 'unlock_date',
        'pin_code', 'share_code', 'category',
        'views', 'reactions', 'is_anniversary',
        'parent_capsule_id', 'chain_order', 'hint'
    ];

    protected $casts = [
        'unlock_date' => 'date:Y-m-d',
        'share_expires_at' => 'datetime',
        'share_revoked_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'views' => 'integer',
        'reactions' => 'array',
        'is_anniversary' => 'boolean',
        'chain_order' => 'integer',
    ];

    // Kategori sabitleri
    public const CATEGORIES = [
        'memory' => ['name' => 'Anı', 'icon' => '💭', 'color' => 'indigo'],
        'gift' => ['name' => 'Hediye', 'icon' => '🎁', 'color' => 'rose'],
        'mystery' => ['name' => 'Gizem', 'icon' => '🔮', 'color' => 'violet'],
        'game' => ['name' => 'Oyun', 'icon' => '🎮', 'color' => 'emerald'],
        'anniversary' => ['name' => 'Yıldönümü', 'icon' => '🎂', 'color' => 'amber'],
        'treasure' => ['name' => 'Hazine', 'icon' => '💎', 'color' => 'cyan'],
    ];

    // Tepki emojileri
    public const REACTION_EMOJIS = ['❤️', '😍', '🔥', '👏', '😢', '😮'];

    /**
     * Kullanıcı ilişkisi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Üst kapsül (hazine avı zinciri)
     */
    public function parentCapsule(): BelongsTo
    {
        return $this->belongsTo(Capsule::class, 'parent_capsule_id');
    }

    /**
     * Alt kapsüller (hazine avı zinciri)
     */
    public function childCapsules(): HasMany
    {
        return $this->hasMany(Capsule::class, 'parent_capsule_id')->orderBy('chain_order');
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
        $this->share_expires_at = now()->addDays(30);
        $this->share_revoked_at = null;
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
     * PIN değerini güvenli olarak sakla.
     */
    public function setPinCodeAttribute(?string $value): void
    {
        if (empty($value)) {
            $this->attributes['pin_code'] = null;
            return;
        }

        $this->attributes['pin_code'] = password_get_info($value)['algo']
            ? $value
            : Hash::make($value);
    }

    /**
     * PIN doğrula (eski düz metin kayıtlarla uyumlu).
     */
    public function verifyPin(string $inputPin): bool
    {
        $storedPin = (string) $this->pin_code;

        if ($storedPin === '') {
            return false;
        }

        if (password_get_info($storedPin)['algo']) {
            return Hash::check($inputPin, $storedPin);
        }

        return hash_equals($storedPin, $inputPin);
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
     * Yıldönümü kapsülü bugün açılabilir mi?
     */
    public function getIsAnniversaryUnlockedAttribute(): bool
    {
        if (!$this->is_anniversary || !$this->unlock_date) return true;
        
        $today = now();
        return $today->month === $this->unlock_date->month 
            && $today->day === $this->unlock_date->day;
    }

    /**
     * Kategori bilgisi
     */
    public function getCategoryInfoAttribute(): array
    {
        return self::CATEGORIES[$this->category] ?? self::CATEGORIES['memory'];
    }

    /**
     * Hazine avı zincirinde mi?
     */
    public function getIsInTreasureHuntAttribute(): bool
    {
        return $this->category === 'treasure' && ($this->parent_capsule_id || $this->childCapsules()->exists());
    }

    /**
     * Zincirdeki önceki kapsül açılmış mı?
     */
    public function isPreviousInChainOpened(User $user): bool
    {
        if (!$this->parent_capsule_id) return true;
        
        // Burada görüntüleme takibi yapılması gerekir
        // Şimdilik basit mantık
        return $this->parentCapsule->views > 0;
    }

    /**
     * Tepki ekle
     */
    public function addReaction(string $emoji): void
    {
        if (!in_array($emoji, self::REACTION_EMOJIS)) return;

        $reactions = $this->reactions ?? [];
        $reactions[$emoji] = ($reactions[$emoji] ?? 0) + 1;
        
        $this->update(['reactions' => $reactions]);
    }

    /**
     * Görüntülenme sayısını artır
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    /**
     * Toplam tepki sayısı
     */
    public function getTotalReactionsAttribute(): int
    {
        return array_sum($this->reactions ?? []);
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
     * Mesafe (kilometre)
     */
    public function distanceFromKm(float $lat, float $lng): float
    {
        return $this->distanceFrom($lat, $lng) / 1000;
    }

    /**
     * Kategoriye göre açılma mesafe limiti (metre)
     */
    public function requiredDistanceMeters(): int
    {
        return match ($this->category) {
            'game' => 50,
            'treasure' => 30,
            'mystery' => 75,
            default => 100,
        };
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

    /**
     * Scope: Kategoriye göre
     */
    public function scopeCategory($query, ?string $category)
    {
        if (!$category) return $query;

        return $query->where('category', $category);
    }

    /**
     * Scope: Hazine avı zinciri
     */
    public function scopeTreasureHuntRoot($query)
    {
        return $query->where('category', 'treasure')
                     ->whereNull('parent_capsule_id');
    }
}
