<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Capsule;

class CapsuleController extends Controller
{
    public function store(Request $request)
    {
        // Fotoğraf (image) için yeni kurallar ekledik
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Maksimum 5MB fotoğraf
        ]);

        $imagePath = null;

        // Eğer formdan bir fotoğraf geldiyse, onu 'public/capsules' klasörüne kaydet
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('capsules', 'public');
        }

        Capsule::create([
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'image' => $imagePath, // Fotoğrafın yolunu veritabanına yaz
        ]);

        return back()->with('success', 'Kapsül başarıyla gömüldü!');
    }

    public function update(Request $request, Capsule $capsule)
    {
        if ($capsule->user_id !== auth()->id()) { abort(403); }

        $validated = $request->validate(['message' => 'required|string|max:1000']);
        $capsule->update($validated);
        return back();
    }

    public function destroy(Capsule $capsule)
    {
        if ($capsule->user_id !== auth()->id()) { abort(403); }
        $capsule->delete();
        return back();
    }
}
