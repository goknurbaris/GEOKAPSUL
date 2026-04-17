<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Badge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile dashboard.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $user->loadCount(['badges', 'capsules']);
        $recentBadges = $user->badges()
            ->withPivot('earned_at')
            ->orderByDesc('user_badges.earned_at')
            ->limit(6)
            ->get();
        $capsulesByCategory = $user->capsules()
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();
        $nextBadgeProgress = $this->resolveNextBadgeProgress($user, $capsulesByCategory);

        return view('profile.show', [
            'user' => $user,
            'recentBadges' => $recentBadges,
            'capsulesByCategory' => $capsulesByCategory,
            'nextBadgeProgress' => $nextBadgeProgress,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($request->boolean('remove_avatar') && $user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->avatar_path = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();

        DB::transaction(function () use ($user): void {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path) && !Storage::disk('public')->delete($user->avatar_path)) {
                throw new RuntimeException('Profil fotoğrafı silinemedi.');
            }

            $user->delete();
        });

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    private function resolveNextBadgeProgress($user, array $capsulesByCategory): ?array
    {
        $earnedBadgeIds = $user->badges()->pluck('badges.id');
        $candidateBadges = Badge::query()
            ->whereNotIn('id', $earnedBadgeIds)
            ->get();

        $bestMatch = null;

        foreach ($candidateBadges as $badge) {
            $criteria = $badge->criteria ?? [];
            $target = (int) ($criteria['value'] ?? 0);
            if ($target <= 0) {
                continue;
            }

            $current = match ($criteria['type'] ?? null) {
                'capsule_count' => (int) $user->capsules_created,
                'capsule_opened' => (int) $user->capsules_opened,
                'distance' => (float) $user->total_distance_km,
                'level' => (int) $user->level,
                'category' => (int) ($capsulesByCategory[$criteria['category'] ?? ''] ?? 0),
                default => null,
            };

            if ($current === null) {
                continue;
            }

            $remaining = max(0, $target - $current);
            $progress = min(100, (int) floor(($current / $target) * 100));
            $candidate = [
                'badge' => $badge,
                'current' => $current,
                'target' => $target,
                'remaining' => $remaining,
                'progress' => $progress,
            ];

            if ($bestMatch === null) {
                $bestMatch = $candidate;
                continue;
            }

            if ($candidate['progress'] > $bestMatch['progress']
                || ($candidate['progress'] === $bestMatch['progress'] && $candidate['remaining'] < $bestMatch['remaining'])) {
                $bestMatch = $candidate;
            }
        }

        return $bestMatch;
    }
}
