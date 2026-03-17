<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Capsule;
use Illuminate\Support\Facades\Storage;

class CapsuleController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'audio' => 'nullable|file|max:20480', // SES DOSYASI İÇİN KONTROL (Maksimum 20MB)
            'unlock_date' => 'nullable|date|after_or_equal:today',
            'pin_code' => 'nullable|numeric|digits:4',
        ]);

        $kapsul = new Capsule();
        $kapsul->user_id = auth()->id();
        $kapsul->message = $validated['message'];
        $kapsul->latitude = $validated['latitude'];
        $kapsul->longitude = $validated['longitude'];
        $kapsul->unlock_date = $validated['unlock_date'] ?? null;
        $kapsul->pin_code = $validated['pin_code'] ?? null;

        if ($request->hasFile('image')) {
            $kapsul->image = $request->file('image')->store('capsules/images', 'public');
        }

        if ($request->hasFile('audio')) {
            $kapsul->audio = $request->file('audio')->store('capsules/audios', 'public');
        }

        $kapsul->save();
        return back();
    }

    public function update(Request $request, Capsule $capsule)
    {
        if ($capsule->user_id !== auth()->id()) { abort(403); }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'audio' => 'nullable|file|max:20480',
            'unlock_date' => 'nullable|date',
            'pin_code' => 'nullable|numeric|digits:4',
        ]);

        $capsule->message = $validated['message'];
        if (isset($validated['unlock_date'])) $capsule->unlock_date = $validated['unlock_date'];
        if ($request->has('pin_code')) $capsule->pin_code = $validated['pin_code'];

        if ($request->hasFile('image')) {
            if ($capsule->image) Storage::disk('public')->delete($capsule->image);
            $capsule->image = $request->file('image')->store('capsules/images', 'public');
        }

        if ($request->hasFile('audio')) {
            if ($capsule->audio) Storage::disk('public')->delete($capsule->audio);
            $capsule->audio = $request->file('audio')->store('capsules/audios', 'public');
        }

        $capsule->save();
        return back();
    }

    public function destroy(Capsule $capsule)
    {
        if ($capsule->user_id !== auth()->id()) { abort(403); }

        // Silinirken dosyaları da sunucudan temizle (Yer kaplamasın)
        if ($capsule->image) Storage::disk('public')->delete($capsule->image);
        if ($capsule->audio) Storage::disk('public')->delete($capsule->audio);

        $capsule->delete();
        return back();
    }
}
