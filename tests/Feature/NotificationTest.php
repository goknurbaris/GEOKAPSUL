<?php

use App\Models\Capsule;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

test('authenticated user can list notifications', function () {
    $user = User::factory()->create();

    Notification::create([
        'user_id' => $user->id,
        'type' => 'capsule-created',
        'title' => 'Test Bildirim',
        'body' => 'Bildirim metni',
        'action_url' => route('dashboard'),
    ]);

    $response = $this->actingAs($user)->getJson(route('api.notifications'));

    $response->assertOk();
    expect($response->json('unread_count'))->toBe(1);
    expect($response->json('items.0.title'))->toBe('Test Bildirim');
});

test('notifications endpoint supports unread filter', function () {
    $user = User::factory()->create();

    Notification::create([
        'user_id' => $user->id,
        'type' => 'capsule-created',
        'title' => 'Okunmamis',
        'read_at' => null,
    ]);

    Notification::create([
        'user_id' => $user->id,
        'type' => 'capsule-created',
        'title' => 'Okunmus',
        'read_at' => now(),
    ]);

    $response = $this->actingAs($user)->getJson(route('api.notifications', ['filter' => 'unread']));

    $response->assertOk();
    expect(collect($response->json('items'))->pluck('title')->all())->toBe(['Okunmamis']);
});

test('authenticated user can mark notifications as read', function () {
    $user = User::factory()->create();

    Notification::create([
        'user_id' => $user->id,
        'type' => 'capsule-created',
        'title' => 'Test Bildirim',
    ]);

    $response = $this->actingAs($user)->post(route('api.notifications.read-all'));

    $response->assertNoContent();
    expect($user->notifications()->whereNull('read_at')->count())->toBe(0);
});

test('unlock reminder command creates notifications for today and tomorrow capsules', function () {
    $user = User::factory()->create();

    Capsule::factory()->create([
        'user_id' => $user->id,
        'unlock_date' => now()->format('Y-m-d'),
        'category' => 'memory',
        'message' => 'Bugun acilacak kapsul',
    ]);

    Capsule::factory()->create([
        'user_id' => $user->id,
        'unlock_date' => now()->addDay()->format('Y-m-d'),
        'category' => 'anniversary',
        'message' => 'Yarin yildonumu kapsulu',
    ]);

    Artisan::call('capsules:send-unlock-reminders');

    expect(Notification::query()->where('type', 'capsule-unlock-reminder')->count())->toBe(2);
});

test('unlock reminder command does not duplicate reminders on same day', function () {
    $user = User::factory()->create();
    $capsule = Capsule::factory()->create([
        'user_id' => $user->id,
        'unlock_date' => now()->format('Y-m-d'),
        'category' => 'memory',
        'message' => 'Tekrarsiz hatirlatma testi',
    ]);

    Artisan::call('capsules:send-unlock-reminders');
    Artisan::call('capsules:send-unlock-reminders');

    $actionUrl = route('dashboard', ['capsule' => $capsule->id]);
    expect(Notification::query()
        ->where('user_id', $user->id)
        ->where('type', 'capsule-unlock-reminder')
        ->where('action_url', $actionUrl)
        ->count())->toBe(1);
});
