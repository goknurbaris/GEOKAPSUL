<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Capsule;

class CapsuleController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Capsule::create([
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        return back();
    }

    // GÜNCELLEME ÖZELLİĞİ
    public function update(Request $request, Capsule $capsule)
    {
        // Güvenlik: Sadece kapsülün sahibi güncelleyebilir
        if ($capsule->user_id !== auth()->id()) { abort(403); }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $capsule->update($validated);
        return back();
    }

    // SİLME ÖZELLİĞİ
    public function destroy(Capsule $capsule)
    {
        // Güvenlik: Sadece kapsülün sahibi silebilir
        if ($capsule->user_id !== auth()->id()) { abort(403); }

        $capsule->delete();
        return back();
    }
}
