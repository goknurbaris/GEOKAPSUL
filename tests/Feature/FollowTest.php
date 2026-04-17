<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can follow and unfollow another user', function () {
    $follower = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($follower)
        ->post(route('follow.store', $target))
        ->assertRedirect();

    expect($follower->following()->where('users.id', $target->id)->exists())->toBeTrue();

    $this->actingAs($follower)
        ->delete(route('follow.destroy', $target))
        ->assertRedirect();

    expect($follower->following()->where('users.id', $target->id)->exists())->toBeFalse();
});

test('user cannot follow themselves', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('follow.store', $user))
        ->assertRedirect();

    expect($user->following()->where('users.id', $user->id)->exists())->toBeFalse();
});

test('guest cannot follow users', function () {
    $user = User::factory()->create();

    $this->post(route('follow.store', $user))
        ->assertRedirect(route('login'));
});

