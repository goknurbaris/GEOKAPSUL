<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'icon',
        'color',
        'xp_reward',
        'criteria',
    ];

    protected $casts = [
        'criteria' => 'array',
        'xp_reward' => 'integer',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
                    ->withPivot('earned_at');
    }

    // Rozet türleri
    public const TYPES = [
        'capsule_count' => 'Kapsül Sayısı',
        'capsule_opened' => 'Açılan Kapsül',
        'distance' => 'Mesafe (km)',
        'streak' => 'Gün Serisi',
        'category' => 'Kategori Uzmanı',
        'special' => 'Özel Başarı',
    ];

    // Renk sınıfları
    public function getColorClassesAttribute(): array
    {
        return match($this->color) {
            'gold' => ['bg-gradient-to-br from-amber-400 to-yellow-600', 'text-amber-400', 'shadow-amber-500/30'],
            'silver' => ['bg-gradient-to-br from-slate-300 to-slate-500', 'text-slate-300', 'shadow-slate-500/30'],
            'bronze' => ['bg-gradient-to-br from-orange-400 to-orange-700', 'text-orange-400', 'shadow-orange-500/30'],
            'emerald' => ['bg-gradient-to-br from-emerald-400 to-green-600', 'text-emerald-400', 'shadow-emerald-500/30'],
            'violet' => ['bg-gradient-to-br from-violet-400 to-purple-600', 'text-violet-400', 'shadow-violet-500/30'],
            'rose' => ['bg-gradient-to-br from-rose-400 to-pink-600', 'text-rose-400', 'shadow-rose-500/30'],
            'cyan' => ['bg-gradient-to-br from-cyan-400 to-blue-600', 'text-cyan-400', 'shadow-cyan-500/30'],
            default => ['bg-gradient-to-br from-indigo-400 to-indigo-600', 'text-indigo-400', 'shadow-indigo-500/30'],
        };
    }
}
