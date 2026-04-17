<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCapsuleRequest;
use App\Http\Requests\UpdateCapsuleRequest;
use Illuminate\Http\Request;
use App\Models\Capsule;
use App\Models\Notification;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CapsuleController extends Controller
{
    /**
     * Dashboard - Kapsüllerimi listele (sayfalama, arama ve kategori filtresi ile)
     */
    public function dashboard(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $sort = $request->input('sort', 'newest');
        $allowedSorts = ['newest', 'oldest', 'unlock_soon'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'newest';
        }
        $perPage = 12;

        $query = Capsule::forUser(auth()->id())
            ->search($search)
            ->category($category);

        if ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'unlock_soon') {
            $query->orderByRaw('unlock_date IS NULL')->orderBy('unlock_date');
        } else {
            $query->latest();
        }

        $myCapsules = $query
            ->paginate($perPage)
            ->withQueryString();

        $baseQuery = Capsule::forUser(auth()->id());
        $totalCapsules = (int) $baseQuery->count();
        $scheduledCapsules = (int) (clone $baseQuery)->whereNotNull('unlock_date')->count();
        $pinProtectedCapsules = (int) (clone $baseQuery)->whereNotNull('pin_code')->count();

        $categorySummary = (clone $baseQuery)
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        return view('dashboard', compact(
            'myCapsules',
            'search',
            'category',
            'sort',
            'totalCapsules',
            'scheduledCapsules',
            'pinProtectedCapsules',
            'categorySummary'
        ));
    }

    /**
     * Kapsül içeriğini getir (PIN, tarih kilidi ve mesafe kontrolü ile)
     */
    public function show(Request $request, Capsule $capsule)
    {
        // Server-side mesafe kontrolü (kategoriye göre dinamik)
        $userLat = $request->input('lat');
        $userLng = $request->input('lng');
        $distanceKm = 0;
        $requiredDistance = $capsule->requiredDistanceMeters();

        if ($userLat && $userLng) {
            $distance = $capsule->distanceFrom((float) $userLat, (float) $userLng);
            $distanceKm = $distance / 1000;

            if ($distance > $requiredDistance) {
                return response()->json([
                    'locked' => true,
                    'lock_type' => 'distance',
                    'distance' => round($distance),
                    'required_distance' => $requiredDistance,
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

        // Yıldönümü kontrolü
        if ($capsule->is_anniversary && !$capsule->is_anniversary_unlocked) {
            return response()->json([
                'locked' => true,
                'lock_type' => 'anniversary',
                'message' => 'Bu yıldönümü kapsülü sadece ' . $capsule->unlock_date->format('d M') . ' tarihinde açılabilir.'
            ]);
        }

        // PIN kontrolü
        if ($capsule->has_pin) {
            $inputPin = $request->input('pin');
            $pinLimitKey = $this->pinRateLimitKey($request, $capsule);
            $maxAttempts = $capsule->category === 'game' ? 3 : 5;
            $lockSeconds = $capsule->category === 'game' ? 900 : 600;

            if (!$inputPin) {
                return response()->json([
                    'locked' => true,
                    'lock_type' => 'pin',
                    'category' => $capsule->category,
                    'message' => $capsule->category === 'game'
                        ? 'Bu oyun kapsülü PIN korumalı. Görevi çözmek için doğru kodu gir.'
                        : 'Bu kapsül şifre korumalı.'
                ]);
            }

            if (RateLimiter::tooManyAttempts($pinLimitKey, $maxAttempts)) {
                return response()->json([
                    'locked' => true,
                    'lock_type' => 'pin',
                    'category' => $capsule->category,
                    'error' => 'Çok fazla deneme yaptın.',
                    'retry_after' => RateLimiter::availableIn($pinLimitKey),
                    'message' => 'Lütfen biraz bekleyip tekrar dene.'
                ], 429);
            }

            if (!$capsule->verifyPin($inputPin)) {
                RateLimiter::hit($pinLimitKey, $lockSeconds);
                $attemptsLeft = max(0, $maxAttempts - RateLimiter::attempts($pinLimitKey));

                return response()->json([
                    'locked' => true,
                    'lock_type' => 'pin',
                    'category' => $capsule->category,
                    'error' => 'Hatalı şifre!',
                    'attempts_left' => $attemptsLeft,
                    'message' => $capsule->category === 'game'
                        ? 'Kod yanlış. Kalan deneme hakkın: ' . $attemptsLeft
                        : 'Girdiğin şifre yanlış.'
                ]);
            }

            RateLimiter::clear($pinLimitKey);
        }

        // XP kazan (giriş yapmışsa)
        $gamificationResult = null;
        if (auth()->check()) {
            $gamificationResult = GamificationService::onCapsuleOpened(auth()->user(), $capsule, $distanceKm);
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
                'category' => $capsule->category_info,
                'views' => $capsule->views,
                'reactions' => $capsule->reactions ?? [],
            ];
        });

        // Görüntülenme sayısını güncelle (cache dışı)
        $content['views'] = $capsule->fresh()->views;

        $response = [
            'locked' => false,
            'capsule' => $content
        ];

        if ($gamificationResult) {
            $response['xp_gained'] = $gamificationResult['xp_gained'];
            $response['new_badges'] = collect($gamificationResult['new_badges'])->map(fn($b) => [
                'name' => $b->name,
                'icon' => $b->icon,
                'xp_reward' => $b->xp_reward
            ]);
        }

        return response()->json($response);
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
            $pinLimitKey = $this->pinRateLimitKey($request, $capsule);

            if (RateLimiter::tooManyAttempts($pinLimitKey, 5)) {
                return view('auth.shared-capsule', [
                    'locked' => true,
                    'lock_type' => 'pin',
                    'error' => 'Çok fazla deneme yaptın. Lütfen biraz bekleyip tekrar dene.',
                    'shareCode' => $shareCode,
                    'capsule' => null
                ]);
            }

            if (!$inputPin || !$capsule->verifyPin($inputPin)) {
                if ($inputPin) {
                    RateLimiter::hit($pinLimitKey, 600);
                }

                return view('auth.shared-capsule', [
                    'locked' => true,
                    'lock_type' => 'pin',
                    'error' => $inputPin ? 'Hatalı şifre!' : null,
                    'shareCode' => $shareCode,
                    'capsule' => null
                ]);
            }

            RateLimiter::clear($pinLimitKey);
        }

        // Görüntülenme artır
        $capsule->incrementViews();

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
     * Kapsüle tepki ekle
     */
    public function addReaction(Request $request, Capsule $capsule)
    {
        $emoji = $request->input('emoji');

        if (!in_array($emoji, Capsule::REACTION_EMOJIS)) {
            return response()->json(['error' => 'Geçersiz emoji'], 400);
        }

        $capsule->addReaction($emoji);

        return response()->json([
            'success' => true,
            'reactions' => $capsule->fresh()->reactions
        ]);
    }

    /**
     * Kapsül oluştur
     */
    public function store(StoreCapsuleRequest $request)
    {
        $validated = $request->validated();

        $kapsul = new Capsule();
        $kapsul->user_id = auth()->id();
        $kapsul->message = $validated['message'];
        $kapsul->latitude = $validated['latitude'];
        $kapsul->longitude = $validated['longitude'];
        $kapsul->unlock_date = $validated['unlock_date'] ?? null;
        $kapsul->pin_code = $validated['pin_code'] ?? null;
        $kapsul->category = $validated['category'] ?? 'memory';
        $kapsul->is_anniversary = $kapsul->category === 'anniversary';
        $kapsul->hint = $validated['hint'] ?? null;

        // Resim işleme ve optimizasyon
        if ($request->hasFile('image')) {
            $kapsul->image = $this->processAndStoreImage($request->file('image'));
        }

        // Ses dosyası
        if ($request->hasFile('audio')) {
            $kapsul->audio = $request->file('audio')->store('capsules/audios', 'public');
        }

        $kapsul->save();

        if (in_array($kapsul->category, ['anniversary', 'gift'], true)) {
            Notification::create([
                'user_id' => auth()->id(),
                'type' => 'capsule-created',
                'title' => 'Kapsül bildirimi',
                'body' => $kapsul->category === 'anniversary'
                    ? 'Yıldönümü kapsülün oluşturuldu. Tarih gelince açılabilir olacak.'
                    : 'Hediye kapsülün oluşturuldu. Paylaşmayı unutma!',
                'action_url' => route('dashboard'),
            ]);
        }

        // XP kazan
        $gamificationResult = GamificationService::onCapsuleCreated(auth()->user(), $kapsul);

        $message = 'Kapsül başarıyla oluşturuldu! 🎉';
        if ($gamificationResult['xp_gained'] > 0) {
            $message .= ' +' . $gamificationResult['xp_gained'] . ' XP';
        }
        if (!empty($gamificationResult['new_badges'])) {
            $badgeNames = collect($gamificationResult['new_badges'])->pluck('name')->join(', ');
            $message .= ' | Yeni rozet: ' . $badgeNames;
        }

        return back()->with('success', $message);
    }

    /**
     * Kapsül güncelle
     */
    public function update(UpdateCapsuleRequest $request, Capsule $capsule)
    {
        if ($capsule->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validated();

        $capsule->message = $validated['message'];
        $capsule->unlock_date = !empty($validated['unlock_date']) ? $validated['unlock_date'] : null;
        $capsule->pin_code = !empty($validated['pin_code']) ? $validated['pin_code'] : null;
        $capsule->category = $validated['category'] ?? $capsule->category;
        $capsule->is_anniversary = $capsule->category === 'anniversary';
        $capsule->hint = $validated['hint'] ?? $capsule->hint;

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
        } catch (\Throwable $e) {
            // Intervention başarısız olursa normal kaydet
            return $file->store('capsules/images', 'public');
        }
    }

    private function pinRateLimitKey(Request $request, Capsule $capsule): string
    {
        return 'pin-attempt:' . $capsule->id . ':' . $request->ip();
    }
}
