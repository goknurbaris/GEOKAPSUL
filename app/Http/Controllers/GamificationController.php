<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\Request;

class GamificationController extends Controller
{
    // XP Ödülleri
    public const XP_REWARDS = [
        'capsule_create' => 25,
        'capsule_open' => 10,
        'capsule_share' => 15,
        'treasure_hunt_complete' => 100,
    ];

    /**
     * Liderlik tablosu sayfası
     */
    public function leaderboard(Request $request)
    {
        $type = $request->get('type', 'xp');

        $leaderboards = [
            'xp' => User::orderByDesc('xp')->limit(50)->get(),
            'capsules' => User::orderByDesc('capsules_created')->limit(50)->get(),
            'explorer' => User::orderByDesc('capsules_opened')->limit(50)->get(),
            'distance' => User::orderByDesc('total_distance_km')->limit(50)->get(),
        ];

        $user = auth()->user();
        $userRanks = [];

        if ($user) {
            $userRanks = [
                'xp' => User::where('xp', '>', $user->xp)->count() + 1,
                'capsules' => User::where('capsules_created', '>', $user->capsules_created)->count() + 1,
                'explorer' => User::where('capsules_opened', '>', $user->capsules_opened)->count() + 1,
                'distance' => User::where('total_distance_km', '>', $user->total_distance_km)->count() + 1,
            ];
        }

        return view('gamification.leaderboard', [
            'leaderboards' => $leaderboards,
            'currentType' => $type,
            'userRanks' => $userRanks,
            'user' => $user,
        ]);
    }

    /**
     * Rozetler sayfası
     */
    public function badges()
    {
        $user = auth()->user();
        $allBadges = Badge::all();

        $earnedBadgeIds = $user ? $user->badges()->pluck('badge_id')->toArray() : [];

        // Gruplandırılmış rozetler
        $groupedBadges = $allBadges->groupBy(function ($badge) {
            return $badge->criteria['type'] ?? 'special';
        });

        return view('gamification.badges', [
            'groupedBadges' => $groupedBadges,
            'earnedBadgeIds' => $earnedBadgeIds,
            'user' => $user,
            'totalBadges' => $allBadges->count(),
            'earnedCount' => count($earnedBadgeIds),
        ]);
    }

    /**
     * Kullanıcı profil istatistikleri (AJAX)
     */
    public function stats()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'xp' => $user->xp,
            'level' => $user->level,
            'level_title' => $user->level_title,
            'level_progress' => $user->levelProgress(),
            'xp_for_next' => $user->xpForNextLevel(),
            'capsules_created' => $user->capsules_created,
            'capsules_opened' => $user->capsules_opened,
            'total_distance_km' => round($user->total_distance_km, 2),
            'badges_count' => $user->badges()->count(),
            'rank' => User::where('xp', '>', $user->xp)->count() + 1,
        ]);
    }

    /**
     * Kapsül oluşturulduğunda XP ver
     */
    public static function awardCapsuleCreation(User $user, $capsule): array
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

    /**
     * Kapsül açıldığında XP ver
     */
    public static function awardCapsuleOpening(User $user, $capsule, float $distanceKm = 0): array
    {
        $xpGained = self::XP_REWARDS['capsule_open'];

        if ($distanceKm > 1) {
            $xpGained += min(50, (int) ($distanceKm * 5));
        }

        if ($capsule->category === 'treasure' && !$capsule->childCapsules()->exists()) {
            $xpGained += self::XP_REWARDS['treasure_hunt_complete'];
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
