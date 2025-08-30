<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

it('healthz 200', function () {
    $this->getJson('/api/healthz')->assertOk()->assertJson(['ok' => true]);
});

it('ready 200', function () {
    $this->getJson('/api/ready')->assertOk()->assertJson(['ok' => true]);
});

it('ready 503 when DB fails', function () {
    DB::shouldReceive('select')->once()->andThrow(new Exception('db down'));
    $this->getJson('/api/ready')->assertStatus(503)->assertJson(['ok' => false]);
});

it('ready 503 when storage fails', function () {

    Storage::shouldReceive('disk')->andThrow(new Exception('storage down'));
    $this->getJson('/api/ready')->assertStatus(503)->assertJson(['ok' => false]);
});
