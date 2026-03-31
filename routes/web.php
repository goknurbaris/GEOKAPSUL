<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CapsuleController;
use App\Models\Capsule;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ANA SAYFA - Herkese Açık Harita
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    // Tüm kapsülleri çekip ana sayfaya gönderiyoruz (içerik hariç - güvenlik için)
    return view('welcome', [
        'capsules' => Capsule::select('id', 'latitude', 'longitude', 'unlock_date', 'created_at')
            ->selectRaw('CASE WHEN pin_code IS NOT NULL THEN 1 ELSE 0 END as has_pin')
            ->get()
    ]);
})->name('welcome');

// Kapsül içeriği API (PIN/tarih kilidi kontrolü ile) - Rate Limited
Route::get('/kapsul/{capsule}', [CapsuleController::class, 'show'])
    ->middleware('throttle:capsule-view')
    ->name('capsule.show');

// Paylaşım linki ile kapsül görüntüleme (herkese açık)
Route::get('/s/{shareCode}', [CapsuleController::class, 'showShared'])->name('capsule.shared');

/*
|--------------------------------------------------------------------------
| PANELİM - Sadece Giriş Yapanlar (Sayfalama ile)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [CapsuleController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| GİRİŞ YAPMIŞ KULLANICI ROTALARI (Profil & Kapsül İşlemleri)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Profil İşlemleri (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // GeoKapsül Kayıt/Güncelleme/Silme - Rate Limited
    Route::post('/kapsul-kaydet', [CapsuleController::class, 'store'])
        ->middleware('throttle:capsule-create')
        ->name('capsule.store');
    Route::patch('/kapsul/{capsule}', [CapsuleController::class, 'update'])->name('capsule.update');
    Route::delete('/kapsul/{capsule}', [CapsuleController::class, 'destroy'])->name('capsule.destroy');
    
    // Paylaşım linki oluştur
    Route::post('/kapsul/{capsule}/share', [CapsuleController::class, 'createShareLink'])->name('capsule.share');

});

// Breeze Kimlik Doğrulama Dosyası (Login/Register buradadır)
require __DIR__.'/auth.php';
