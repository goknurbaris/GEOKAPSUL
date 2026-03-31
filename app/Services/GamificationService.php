<?php

namespace App\Services;

use App\Models\User;
use App\Models\Capsule;

class GamificationService
{
    public const XP_REWARDS = [
        'capsule_create' => 25,
        'capsule_open' => 10,
        'capsule_share' => 15,
        'treasure_hunt_complete' => 100,
        'daily_login' => 5,
    ];

    public static function onCapsuleCreated(User $user, Capsule $capsule): array
    {
        $xpGained = self::XP_REWARDS['capsule_create'];

        if ($capsule->category === 'treasure') {
            $xpGained += 25;
        }

        $user->increment('capsules_created');
        $user->addXp($xpGained);
        $newBadges = $user->checkAndAwardBadges();

        return [
            'xp_gained' => $xpGained,
            'new_badges' => $newBadges,
            'new_level' => $user->level,
        ];
    }

    public static function onCapsuleOpened(User $user, Capsule $capsule, float $distanceKm = 0): array
    {
        $xpGained = self::XP_REWARDS['capsule_open'];

        if ($distanceKm > 1) {
            $xpGained += min(50, (int) ($distanceKm * 5));
        }

        $user->increment('capsules_opened');
        $user->increment('total_distance_km', $distanceKm);
        $user->addXp($xpGained);
        $capsule->incrementViews();

        $newBadges = $user->checkAndAwardBadges();

        return [
            'xp_gained' => $xpGained,
            'new_badges' => $newBadges,
            'new_level' => $user->level,
        ];
    }
}
