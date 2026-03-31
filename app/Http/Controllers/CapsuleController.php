<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Capsule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CapsuleController extends Controller
{
    /**
     * Dashboard - Kapsüllerimi listele (sayfalama ve arama ile)
     */
    public function dashboard(Request $request)
    {
        $search = $request->input('search');
        $perPage = 12;
        
        $myCapsules = Capsule::forUser(auth()->id())
            ->search($search)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('dashboard', compact('myCapsules', 'search'));
    }

    /**
     * Kapsül içeriğini getir (PIN, tarih kilidi ve mesafe kontrolü ile)
     */
    public function show(Request $request, Capsule $capsule)
    {
        // Server-side mesafe kontrolü (100 metre)
        $userLat = $request->input('lat');
        $userLng = $request->input('lng');
        
        if ($userLat && $userLng) {
            $distance = $capsule->distanceFrom((float) $userLat, (float) $userLng);
            
            if ($distance > 100) {
                return response()->json([
                    'locked' => true,
                    'lock_type' => 'distance',
                    'distance' => round($distance),
                    'message' => 'Bu kapsülü açmak için ' . round($distance) . ' metre daha yaklaşmalısın.'
                ]);
            }
        }

        // Tarih kilidi kontrolü
        if ($capsule->is_time_locked) {
            $unlockDate = $capsule->unlock_date->format('d.m.Y');
            return response()->json([
                'locked' => true,
                'lock_type' => 'time',
                'unlock_date' => $unlockDate,
                'message' => 'Bu kapsül ' . $unlockDate . ' tarihine kadar kilitli.'
            ]);
        }

        // PIN kontrolü
        if ($capsule->has_pin) {
            $inputPin = $request->input('pin');
            
            if (!$inputPin) {
                return response()->json([
                    'locked' => true,
                    'lock_type' => 'pin',
                    'message' => 'Bu kapsül şifre korumalı.'
                ]);
            }
            
            if ($inputPin !== $capsule->pin_code) {
                return response()->json([
                    'locked' => true,
                    'lock_type' => 'pin',
                    'error' => 'Hatalı şifre!',
                    'message' => 'Girdiğin şifre yanlış.'
                ]);
            }
        }

        // Başarılı - kapsül içeriğini döndür (cache ile)
        $cacheKey = "capsule_{$capsule->id}_content";
        
        $content = Cache::remember($cacheKey, 3600, function () use ($capsule) {
            return [
                'id' => $capsule->id,
                'message' => $capsule->message,
                'image' => $capsule->image ? asset('storage/' . $capsule->image) : null,
                'audio' => $capsule->audio ? asset('storage/' . $capsule->audio) : null,
                'created_at' => $capsule->created_at->format('d.m.Y H:i'),
            ];
        });

        return response()->json([
            'locked' => false,
            'capsule' => $content
        ]);
    }

    /**
     * Paylaşım linki ile kapsül görüntüle
     */
    public function showShared(Request $request, string $shareCode)
    {
        $capsule = Capsule::where('share_code', $shareCode)->firstOrFail();
        
        // Aynı kontrolleri uygula (mesafe hariç - paylaşımda mesafe yok)
        if ($capsule->is_time_locked) {
            return view('auth.shared-capsule', [
                'locked' => true,
                'lock_type' => 'time',
                'unlock_date' => $capsule->unlock_date->format('d.m.Y'),
                'capsule' => null
            ]);
        }

        // PIN kontrolü
        if ($capsule->has_pin) {
            $inputPin = $request->input('pin');
            
            if (!$inputPin || $inputPin !== $capsule->pin_code) {
                return view('auth.shared-capsule', [
                    'locked' => true,
                    'lock_type' => 'pin',
                    'error' => $inputPin ? 'Hatalı şifre!' : null,
                    'shareCode' => $shareCode,
                    'capsule' => null
                ]);
            }
        }

        return view('auth.shared-capsule', [
            'locked' => false,
            'capsule' => $capsule
        ]);
    }

    /**
     * Paylaşım linki oluştur
     */
    public function createShareLink(Capsule $capsule)
    {
        if ($capsule->user_id !== auth()->id()) {
            abort(403);
        }

        $shareCode = $capsule->share_code ?? $capsule->generateShareCode();
        
        return response()->json([
            'success' => true,
            'share_url' => route('capsule.shared', $shareCode),
            'share_code' => $shareCode
        ]);
    }

    /**
     * Kapsül oluştur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,webm|max:20480',
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

        // Resim işleme ve optimizasyon
        if ($request->hasFile('image')) {
            $kapsul->image = $this->processAndStoreImage($request->file('image'));
        }

        // Ses dosyası
        if ($request->hasFile('audio')) {
            $kapsul->audio = $request->file('audio')->store('capsules/audios', 'public');
        }

        $kapsul->save();
        
        return back()->with('success', 'Kapsül başarıyla oluşturuldu! 🎉');
    }

    /**
     * Kapsül güncelle
     */
    public function update(Request $request, Capsule $capsule)
    {
        if ($capsule->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,webm|max:20480',
            'unlock_date' => 'nullable|date',
            'pin_code' => 'nullable|digits:4',
        ]);

        $capsule->message = $validated['message'];
        $capsule->unlock_date = !empty($validated['unlock_date']) ? $validated['unlock_date'] : null;
        $capsule->pin_code = !empty($validated['pin_code']) ? $validated['pin_code'] : null;

        if ($request->hasFile('image')) {
            if ($capsule->image) {
                Storage::disk('public')->delete($capsule->image);
            }
            $capsule->image = $this->processAndStoreImage($request->file('image'));
        }

        if ($request->hasFile('audio')) {
            if ($capsule->audio) {
                Storage::disk('public')->delete($capsule->audio);
            }
            $capsule->audio = $request->file('audio')->store('capsules/audios', 'public');
        }

        $capsule->save();
        
        // Cache temizle
        Cache::forget("capsule_{$capsule->id}_content");
        
        return back()->with('success', 'Kapsül başarıyla güncellendi! ✨');
    }

    /**
     * Kapsül sil
     */
    public function destroy(Capsule $capsule)
    {
        if ($capsule->user_id !== auth()->id()) {
            abort(403);
        }

        if ($capsule->image) {
            Storage::disk('public')->delete($capsule->image);
        }
        if ($capsule->audio) {
            Storage::disk('public')->delete($capsule->audio);
        }

        // Cache temizle
        Cache::forget("capsule_{$capsule->id}_content");
        
        $capsule->delete();
        
        return back()->with('success', 'Kapsül başarıyla silindi! 🗑️');
    }

    /**
     * Resmi işle ve optimize et
     */
    private function processAndStoreImage($file): string
    {
        $filename = uniqid('capsule_') . '.webp';
        $path = 'capsules/images/' . $filename;
        
        try {
            // Intervention Image ile optimize et
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            
            // Max 1200px genişlik, kalite %80, WebP formatı
            $image->scaleDown(width: 1200);
            $encoded = $image->toWebp(80);
            
            Storage::disk('public')->put($path, $encoded);
            
            return $path;
        } catch (\Exception $e) {
            // Intervention başarısız olursa normal kaydet
            return $file->store('capsules/images', 'public');
        }
    }
}
