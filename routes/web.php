<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Kendi oluşturduğumuz yapıları buraya tanıtıyoruz
use App\Http\Controllers\CapsuleController;
use App\Models\Capsule;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. ANA SAYFA: Haritayı açar ve veritabanındaki tüm kapsülleri haritaya gönderir.
Route::get('/', function () {
    $capsules = Capsule::all();
    return view('welcome', compact('capsules'));
});

// 2. KONTROL PANELİ: Giriş yapmış kullanıcının sadece kendi kapsüllerini listeler.
Route::get('/dashboard', function () {
    $myCapsules = Capsule::where('user_id', auth()->id())->latest()->get();
    return view('dashboard', compact('myCapsules'));
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. KAPSÜL KAYDETME: Haritadan gelen verileri veritabanına yazar.
Route::post('/kapsul-kaydet', [CapsuleController::class, 'store'])->middleware('auth');

// 4. PROFİL AYARLARI: Şifre değiştirme, profil güncelleme vb. (Breeze ile geldi)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 5. GİRİŞ SİSTEMİ: Login, Register gibi sayfaların dosyalarını dahil eder.
require __DIR__.'/auth.php';
Route::patch('/kapsul/{capsule}', [CapsuleController::class, 'update'])->middleware('auth')->name('capsule.update');
Route::delete('/kapsul/{capsule}', [CapsuleController::class, 'destroy'])->middleware('auth')->name('capsule.destroy');
