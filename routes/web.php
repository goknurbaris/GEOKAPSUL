<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CapsuleController;
use App\Models\Capsule;
use Illuminate\Support\Facades\Route;

// 1. ANA SAYFA (Harita Ekranı - Tüm Kapsüller Resimleriyle Birlikte Gider)
Route::get('/', function () {
    return view('welcome', [
        'capsules' => Capsule::all()
    ]);
});

// 2. PANELİM (Sadece Kullanıcının Kendi Kapsülleri)
Route::get('/dashboard', function () {
    return view('dashboard', [
        'myCapsules' => Capsule::where('user_id', auth()->id())->latest()->get()
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. GİRİŞ YAPMIŞ KULLANICI ROTALARI
Route::middleware('auth')->group(function () {

    // Profil İşlemleri (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // GeoKapsül İşlemleri (Ekle, Güncelle, Sil)
    Route::post('/kapsul-kaydet', [CapsuleController::class, 'store'])->name('capsule.store');
    Route::patch('/kapsul/{capsule}', [CapsuleController::class, 'update'])->name('capsule.update');
    Route::delete('/kapsul/{capsule}', [CapsuleController::class, 'destroy'])->name('capsule.destroy');

});

// 4. BREEZE KİMLİK DOĞRULAMA (Login/Register)
require __DIR__.'/auth.php';
