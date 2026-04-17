<?php

use App\Models\User;
use App\Models\Capsule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
});

describe('Capsule CRUD', function () {

    test('authenticated user can create a capsule', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('capsule.store'), [
            'message' => 'Test kapsül mesajı',
            'latitude' => 41.0082,
            'longitude' => 28.9784,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('capsules', [
            'user_id' => $user->id,
            'message' => 'Test kapsül mesajı',
        ]);
    });

    test('capsule can have image and audio', function () {
        $user = User::factory()->create();
        $image = UploadedFile::fake()->image('photo.jpg');
        $audio = UploadedFile::fake()->create('audio.mp3', 1000, 'audio/mpeg');

        $response = $this->actingAs($user)->post(route('capsule.store'), [
            'message' => 'Medyalı kapsül',
            'latitude' => 41.0082,
            'longitude' => 28.9784,
            'image' => $image,
            'audio' => $audio,
        ]);

        $response->assertRedirect();

        $capsule = Capsule::where('user_id', $user->id)->first();
        expect($capsule->image)->not->toBeNull();
        expect($capsule->audio)->not->toBeNull();
    });

    test('capsule can have pin code', function () {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('capsule.store'), [
            'message' => 'PIN korumalı',
            'latitude' => 41.0082,
            'longitude' => 28.9784,
            'pin_code' => '1234',
        ]);

        $capsule = Capsule::where('user_id', $user->id)->latest()->first();
        expect($capsule)->not->toBeNull();
        expect(Hash::check('1234', $capsule->pin_code))->toBeTrue();
    });

    test('capsule can have unlock date', function () {
        $user = User::factory()->create();
        $futureDate = now()->addDays(7)->format('Y-m-d');

        $this->actingAs($user)->post(route('capsule.store'), [
            'message' => 'Tarih kilitli',
            'latitude' => 41.0082,
            'longitude' => 28.9784,
            'unlock_date' => $futureDate,
        ]);

        $this->assertDatabaseHas('capsules', [
            'unlock_date' => $futureDate,
        ]);
    });

    test('user can update their own capsule', function () {
        $user = User::factory()->create();
        $capsule = Capsule::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->patch(route('capsule.update', $capsule), [
            'message' => 'Güncellenmiş mesaj',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('capsules', [
            'id' => $capsule->id,
            'message' => 'Güncellenmiş mesaj',
        ]);
    });

    test('user cannot update other users capsule', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $capsule = Capsule::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->patch(route('capsule.update', $capsule), [
            'message' => 'Hack attempt',
        ]);

        $response->assertForbidden();
    });

    test('user can delete their own capsule', function () {
        $user = User::factory()->create();
        $capsule = Capsule::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('capsule.destroy', $capsule));

        $response->assertRedirect();
        $this->assertDatabaseMissing('capsules', ['id' => $capsule->id]);
    });

    test('user cannot delete other users capsule', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $capsule = Capsule::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->delete(route('capsule.destroy', $capsule));

        $response->assertForbidden();
        $this->assertDatabaseHas('capsules', ['id' => $capsule->id]);
    });
});

describe('Capsule Access Control', function () {

    test('capsule with pin requires correct pin', function () {
        $capsule = Capsule::factory()->create(['pin_code' => Hash::make('1234')]);

        // PIN olmadan
        $response = $this->getJson(route('capsule.show', $capsule));
        expect($response->json('locked'))->toBeTrue();
        expect($response->json('lock_type'))->toBe('pin');

        // Yanlış PIN
        $response = $this->getJson(route('capsule.show', $capsule) . '?pin=0000');
        expect($response->json('locked'))->toBeTrue();
        expect($response->json('error'))->toBe('Hatalı şifre!');

        // Doğru PIN
        $response = $this->getJson(route('capsule.show', $capsule) . '?pin=1234');
        expect($response->json('locked'))->toBeFalse();
        expect($response->json('capsule'))->not->toBeNull();
    });

    test('capsule pin gets rate limited after too many failed attempts', function () {
        $capsule = Capsule::factory()->create(['pin_code' => Hash::make('1234')]);

        for ($i = 0; $i < 5; $i++) {
            $this->getJson(route('capsule.show', $capsule) . '?pin=0000')
                ->assertOk();
        }

        $response = $this->getJson(route('capsule.show', $capsule) . '?pin=0000');

        $response->assertStatus(429);
        expect($response->json('error'))->toBe('Çok fazla deneme yaptın.');
        expect($response->json('retry_after'))->toBeInt();
    });

    test('game capsule pin gets rate limited after three failed attempts', function () {
        $capsule = Capsule::factory()->create([
            'category' => 'game',
            'pin_code' => Hash::make('1234'),
            'hint' => 'Dort haneli bir kod',
        ]);

        for ($i = 0; $i < 3; $i++) {
            $this->getJson(route('capsule.show', $capsule) . '?pin=0000')
                ->assertOk()
                ->assertJsonPath('attempts_left', 2 - $i);
        }

        $response = $this->getJson(route('capsule.show', $capsule) . '?pin=0000');
        $response->assertStatus(429);
        expect($response->json('error'))->toBe('Çok fazla deneme yaptın.');
    });

    test('pin lock responses include capsule category', function () {
        $capsule = Capsule::factory()->create([
            'category' => 'game',
            'pin_code' => Hash::make('1234'),
            'hint' => 'Kategori yaniti',
        ]);

        $withoutPin = $this->getJson(route('capsule.show', $capsule));
        $withoutPin->assertOk();
        expect($withoutPin->json('category'))->toBe('game');

        $wrongPin = $this->getJson(route('capsule.show', $capsule) . '?pin=0000');
        $wrongPin->assertOk();
        expect($wrongPin->json('category'))->toBe('game');
    });

    test('capsule with future unlock date is locked', function () {
        $capsule = Capsule::factory()->create([
            'unlock_date' => now()->addDays(7),
        ]);

        $response = $this->getJson(route('capsule.show', $capsule));

        expect($response->json('locked'))->toBeTrue();
        expect($response->json('lock_type'))->toBe('time');
    });

    test('capsule with past unlock date is accessible', function () {
        $capsule = Capsule::factory()->create([
            'unlock_date' => now()->subDays(1),
        ]);

        $response = $this->getJson(route('capsule.show', $capsule));

        expect($response->json('locked'))->toBeFalse();
    });

    test('capsule checks distance when coordinates provided', function () {
        $capsule = Capsule::factory()->create([
            'latitude' => 41.0082,
            'longitude' => 28.9784,
        ]);

        // Çok uzak koordinatlar
        $response = $this->getJson(route('capsule.show', $capsule) . '?lat=42.0&lng=29.0');

        expect($response->json('locked'))->toBeTrue();
        expect($response->json('lock_type'))->toBe('distance');
    });

    test('game category uses stricter distance threshold', function () {
        $capsule = Capsule::factory()->create([
            'category' => 'game',
            'latitude' => 41.0082,
            'longitude' => 28.9784,
        ]);

        $response = $this->getJson(route('capsule.show', $capsule) . '?lat=41.0088&lng=28.9784');

        expect($response->json('locked'))->toBeTrue();
        expect($response->json('required_distance'))->toBe(50);
    });
});

describe('Capsule Sharing', function () {

    test('user can create share link', function () {
        $user = User::factory()->create();
        $capsule = Capsule::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson(route('capsule.share', $capsule));

        $response->assertOk();
        expect($response->json('success'))->toBeTrue();
        expect($response->json('share_url'))->toContain('/s/');

        $capsule->refresh();
        expect($capsule->share_code)->not->toBeNull();
    });

    test('user cannot create share link for others capsule', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $capsule = Capsule::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->postJson(route('capsule.share', $capsule));

        $response->assertForbidden();
    });

    test('shared capsule can be accessed via share code', function () {
        $capsule = Capsule::factory()->create();
        $capsule->generateShareCode();

        $response = $this->get(route('capsule.shared', $capsule->share_code));

        $response->assertOk();
    });
});

describe('Dashboard', function () {

    test('dashboard shows only users capsules', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Capsule::factory()->count(3)->create(['user_id' => $user1->id]);
        Capsule::factory()->count(2)->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('myCapsules');
        expect($response->viewData('myCapsules')->total())->toBe(3);
    });

    test('dashboard search filters capsules', function () {
        $user = User::factory()->create();

        Capsule::factory()->create(['user_id' => $user->id, 'message' => 'Doğum günü anısı']);
        Capsule::factory()->create(['user_id' => $user->id, 'message' => 'Tatil fotoğrafı']);

        $response = $this->actingAs($user)->get(route('dashboard', ['search' => 'doğum']));

        expect($response->viewData('myCapsules')->total())->toBe(1);
    });

    test('dashboard paginates results', function () {
        $user = User::factory()->create();
        Capsule::factory()->count(20)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        expect($response->viewData('myCapsules')->perPage())->toBe(12);
    });

    test('dashboard category filter limits capsules by selected category', function () {
        $user = User::factory()->create();

        Capsule::factory()->create([
            'user_id' => $user->id,
            'category' => 'memory',
            'message' => 'Anı kapsülü',
        ]);
        Capsule::factory()->create([
            'user_id' => $user->id,
            'category' => 'gift',
            'message' => 'Hediye kapsülü',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', ['category' => 'gift']));

        expect($response->viewData('myCapsules')->total())->toBe(1);
        expect($response->viewData('category'))->toBe('gift');
    });

    test('dashboard supports oldest sort order', function () {
        $user = User::factory()->create();

        Capsule::factory()->create([
            'user_id' => $user->id,
            'message' => 'Yeni kapsül',
            'created_at' => now(),
        ]);
        Capsule::factory()->create([
            'user_id' => $user->id,
            'message' => 'Eski kapsül',
            'created_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', ['sort' => 'oldest']));

        $capsules = $response->viewData('myCapsules')->values();
        expect($capsules->first()->message)->toBe('Eski kapsül');
    });

    test('dashboard supports unlock soon sort order', function () {
        $user = User::factory()->create();

        Capsule::factory()->create([
            'user_id' => $user->id,
            'message' => 'Yakın tarih',
            'unlock_date' => now()->addDay()->format('Y-m-d'),
        ]);
        Capsule::factory()->create([
            'user_id' => $user->id,
            'message' => 'Uzak tarih',
            'unlock_date' => now()->addDays(7)->format('Y-m-d'),
        ]);
        Capsule::factory()->create([
            'user_id' => $user->id,
            'message' => 'Tarihsiz',
            'unlock_date' => null,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', ['sort' => 'unlock_soon']));

        $capsules = $response->viewData('myCapsules')->values();
        expect($capsules->first()->message)->toBe('Yakın tarih');
        expect($capsules->last()->message)->toBe('Tarihsiz');
    });

    test('dashboard falls back to newest sort for invalid sort value', function () {
        $user = User::factory()->create();

        Capsule::factory()->create([
            'user_id' => $user->id,
            'message' => 'Eski kayıt',
            'created_at' => now()->subDay(),
        ]);
        Capsule::factory()->create([
            'user_id' => $user->id,
            'message' => 'Yeni kayıt',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', ['sort' => 'unknown-value']));

        $capsules = $response->viewData('myCapsules')->values();
        expect($response->viewData('sort'))->toBe('newest');
        expect($capsules->first()->message)->toBe('Yeni kayıt');
    });

    test('dashboard analytics cards data is provided', function () {
        $user = User::factory()->create();

        Capsule::factory()->create([
            'user_id' => $user->id,
            'category' => 'memory',
            'pin_code' => Hash::make('1234'),
        ]);
        Capsule::factory()->create([
            'user_id' => $user->id,
            'category' => 'gift',
            'unlock_date' => now()->addDay()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        expect($response->viewData('totalCapsules'))->toBe(2);
        expect($response->viewData('scheduledCapsules'))->toBe(1);
        expect($response->viewData('pinProtectedCapsules'))->toBe(1);

        $summary = $response->viewData('categorySummary');
        expect($summary)->toBeArray();
        expect($summary['memory'] ?? 0)->toBe(1);
        expect($summary['gift'] ?? 0)->toBe(1);
    });
});

describe('Validation', function () {

    test('capsule requires message', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('capsule.store'), [
            'latitude' => 41.0082,
            'longitude' => 28.9784,
        ]);

        $response->assertSessionHasErrors('message');
    });

    test('capsule requires valid coordinates', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('capsule.store'), [
            'message' => 'Test',
            'latitude' => 'invalid',
            'longitude' => 28.9784,
        ]);

        $response->assertSessionHasErrors('latitude');
    });

    test('pin code must be 4 digits', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('capsule.store'), [
            'message' => 'Test',
            'latitude' => 41.0082,
            'longitude' => 28.9784,
            'pin_code' => '123', // 3 digit - invalid
        ]);

        $response->assertSessionHasErrors('pin_code');
    });

    test('unlock date must be today or future', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('capsule.store'), [
            'message' => 'Test',
            'latitude' => 41.0082,
            'longitude' => 28.9784,
            'unlock_date' => now()->subDay()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('unlock_date');
    });

    test('anniversary category requires unlock date', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('capsule.store'), [
            'message' => 'Yildonumu kapsulu',
            'latitude' => 41.0082,
            'longitude' => 28.9784,
            'category' => 'anniversary',
        ]);

        $response->assertSessionHasErrors('unlock_date');
    });

    test('mystery category requires pin code', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('capsule.store'), [
            'message' => 'Gizem kapsulu',
            'latitude' => 41.0082,
            'longitude' => 28.9784,
            'category' => 'mystery',
        ]);

        $response->assertSessionHasErrors('pin_code');
    });

    test('game category requires both pin code and hint', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('capsule.store'), [
            'message' => 'Oyun kapsulu',
            'latitude' => 41.0082,
            'longitude' => 28.9784,
            'category' => 'game',
        ]);

        $response->assertSessionHasErrors(['pin_code', 'hint']);
    });

    test('treasure category requires hint', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('capsule.store'), [
            'message' => 'Hazine kapsulu',
            'latitude' => 41.0082,
            'longitude' => 28.9784,
            'category' => 'treasure',
        ]);

        $response->assertSessionHasErrors('hint');
    });

    test('treasure category also requires unlock date', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('capsule.store'), [
            'message' => 'Hazine kapsulu',
            'latitude' => 41.0082,
            'longitude' => 28.9784,
            'category' => 'treasure',
            'hint' => 'Agacin dibine bak',
        ]);

        $response->assertSessionHasErrors('unlock_date');
    });

    test('gift category requires pin or unlock date', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('capsule.store'), [
            'message' => 'Hediye kapsulu',
            'latitude' => 41.0082,
            'longitude' => 28.9784,
            'category' => 'gift',
        ]);

        $response->assertSessionHasErrors(['unlock_date', 'pin_code']);
    });

    test('gift category can be created with pin', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('capsule.store'), [
            'message' => 'Hediye kapsulu',
            'latitude' => 41.0082,
            'longitude' => 28.9784,
            'category' => 'gift',
            'pin_code' => '1234',
        ]);

        $response->assertSessionHasNoErrors();
    });
});

describe('Guest Access', function () {

    test('guest cannot create capsule', function () {
        $response = $this->post(route('capsule.store'), [
            'message' => 'Test',
            'latitude' => 41.0082,
            'longitude' => 28.9784,
        ]);

        $response->assertRedirect(route('login'));
    });

    test('guest cannot access dashboard', function () {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    });

    test('guest can view public capsule content', function () {
        $capsule = Capsule::factory()->create();

        $response = $this->getJson(route('capsule.show', $capsule) . '?lat=' . $capsule->latitude . '&lng=' . $capsule->longitude);

        $response->assertOk();
    });
});
