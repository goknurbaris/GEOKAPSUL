<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar_path',
        'password',
        'xp',
        'level',
        'capsules_opened',
        'capsules_created',
        'total_distance_km',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'xp' => 'integer',
            'level' => 'integer',
            'capsules_opened' => 'integer',
            'capsules_created' => 'integer',
            'total_distance_km' => 'decimal:2',
        ];
    }

    /**
     * Get the capsules for the user.
     */
    public function capsules(): HasMany
    {
        return $this->hasMany(Capsule::class);
    }

    /**
     * Get the badges for the user.
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withPivot('earned_at');
    }

    /**
     * Kullanıcı bildirimleri
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    /**
     * XP ekle ve seviye kontrolü yap
     */
    public function addXp(int $amount): void
    {
        $this->increment('xp', $amount);
        $this->checkLevelUp();
    }

    /**
     * Seviye atlama kontrolü
     */
    public function checkLevelUp(): void
    {
        $newLevel = $this->calculateLevel();
        if ($newLevel > $this->level) {
            $this->update(['level' => $newLevel]);
        }
    }

    /**
     * XP'ye göre seviye hesapla
     */
    public function calculateLevel(): int
    {
        // Her seviye için gereken XP: 100 * seviye^1.5
        $level = 1;
        $totalXpNeeded = 0;
        
        while (true) {
            $xpForNextLevel = (int) (100 * pow($level, 1.5));
            $totalXpNeeded += $xpForNextLevel;
            
            if ($this->xp < $totalXpNeeded) {
                break;
            }
            $level++;
            
            if ($level >= 100) break; // Max level
        }
        
        return $level;
    }

    /**
     * Sonraki seviye için gereken XP
     */
    public function xpForNextLevel(): int
    {
        $level = max(1, (int) $this->level);

        return (int) (100 * pow($level, 1.5));
    }

    /**
     * Mevcut seviyedeki ilerleme yüzdesi
     */
    public function levelProgress(): int
    {
        $currentLevel = max(1, (int) $this->level);
        $xpForCurrent = 0;
        for ($i = 1; $i < $currentLevel; $i++) {
            $xpForCurrent += (int) (100 * pow($i, 1.5));
        }
        
        $xpInCurrentLevel = $this->xp - $xpForCurrent;
        $xpNeededForNext = $this->xpForNextLevel();

        if ($xpNeededForNext <= 0) {
            return 0;
        }
        
        return min(100, (int) (($xpInCurrentLevel / $xpNeededForNext) * 100));
    }

    /**
     * Rozet kazandır
     */
    public function awardBadge(Badge $badge): bool
    {
        if ($this->badges()->where('badge_id', $badge->id)->exists()) {
            return false; // Zaten var
        }

        $this->badges()->attach($badge->id, ['earned_at' => now()]);
        
        if ($badge->xp_reward > 0) {
            $this->addXp($badge->xp_reward);
        }

        return true;
    }

    /**
     * Rozet kontrolü ve otomatik kazandırma
     */
    public function checkAndAwardBadges(): array
    {
        $awarded = [];
        $badges = Badge::all();

        foreach ($badges as $badge) {
            if ($this->badges()->where('badge_id', $badge->id)->exists()) {
                continue;
            }

            if ($this->meetsBadgeCriteria($badge)) {
                $this->awardBadge($badge);
                $awarded[] = $badge;
            }
        }

        return $awarded;
    }

    /**
     * Rozet kriterlerini karşılıyor mu?
     */
    protected function meetsBadgeCriteria(Badge $badge): bool
    {
        $criteria = $badge->criteria;
        
        return match($criteria['type'] ?? null) {
            'capsule_count' => $this->capsules_created >= ($criteria['value'] ?? 0),
            'capsule_opened' => $this->capsules_opened >= ($criteria['value'] ?? 0),
            'distance' => $this->total_distance_km >= ($criteria['value'] ?? 0),
            'level' => $this->level >= ($criteria['value'] ?? 0),
            'category' => $this->capsules()->where('category', $criteria['category'] ?? '')->count() >= ($criteria['value'] ?? 0),
            default => false,
        };
    }

    /**
     * Seviye unvanı
     */
    public function getLevelTitleAttribute(): string
    {
        return match(true) {
            $this->level >= 50 => 'Efsane Kaşif',
            $this->level >= 40 => 'Usta Gezgin',
            $this->level >= 30 => 'Deneyimli Kâşif',
            $this->level >= 20 => 'Gezgin',
            $this->level >= 10 => 'Maceracı',
            $this->level >= 5 => 'Keşifçi',
            default => 'Çaylak',
        };
    }

    /**
     * Profil avatarı URL'i
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar_path) {
            return null;
        }

        return Storage::disk('public')->url($this->avatar_path);
    }
}
