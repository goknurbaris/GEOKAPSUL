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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'unlock_date' => 'nullable|date|after_or_equal:today', // TARIH KONTROLÜ
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('capsules', 'public');
        }

        $kapsul = new Capsule();
        $kapsul->user_id = auth()->id();
        $kapsul->message = $validated['message'];
        $kapsul->latitude = $validated['latitude'];
        $kapsul->longitude = $validated['longitude'];
        $kapsul->image = $imagePath;
        $kapsul->unlock_date = $validated['unlock_date'] ?? null; // TARIHI KAYDET
        $kapsul->save();

        return back();
    }

    public function update(Request $request, Capsule $capsule)
    {
        if ($capsule->user_id !== auth()->id()) { abort(403); }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'unlock_date' => 'nullable|date',
        ]);

        $capsule->message = $validated['message'];
        if (isset($validated['unlock_date'])) {
            $capsule->unlock_date = $validated['unlock_date'];
        }

        if ($request->hasFile('image')) {
            $capsule->image = $request->file('image')->store('capsules', 'public');
        }

        $capsule->save();
        return back();
    }

    public function destroy(Capsule $capsule)
    {
        if ($capsule->user_id !== auth()->id()) { abort(403); }
        $capsule->delete();
        return back();
    }
}
