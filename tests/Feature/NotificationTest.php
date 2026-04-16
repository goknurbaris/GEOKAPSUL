<?php

use App\Models\Notification;
use App\Models\User;

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
