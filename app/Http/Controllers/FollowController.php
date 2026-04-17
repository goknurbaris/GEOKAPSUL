<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function store(Request $request, User $user): RedirectResponse
    {
        $authUser = $request->user();

        if ($authUser->id === $user->id) {
            return back()->with('error', 'Kendini takip edemezsin.');
        }

        $authUser->following()->syncWithoutDetaching([$user->id]);

        return back()->with('success', $user->name . ' artık takip ediliyor.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $request->user()->following()->detach($user->id);

        return back()->with('success', $user->name . ' takipten çıkarıldı.');
    }
}

