<?php

use App\Console\Commands\PruneIdempotency;
use App\Console\Commands\PruneRefreshTokens;
use App\Console\Commands\CleanOrphanAttachments;
use App\Models\Attachment;
use App\Models\RefreshToken;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

it('prune:idempotency idempotent', function () {
    DB::table('idempotency_keys')->insert([
        'key' => 'old-1',
        'fingerprint' => str_repeat('a', 40),
        'status' => 201,
        'body' => '{}',
        'created_at' => now()->subDays(10),
        'updated_at' => now()->subDays(10),
    ]);
    DB::table('idempotency_keys')->insert([
        'key' => 'new-1',
        'fingerprint' => str_repeat('b', 40),
        'status' => 201,
        'body' => '{}',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Artisan::call(PruneIdempotency::class, ['--days' => 7]);
    expect(DB::table('idempotency_keys')->where('key','old-1')->exists())->toBeFalse();
    expect(DB::table('idempotency_keys')->where('key','new-1')->exists())->toBeTrue();

    Artisan::call(PruneIdempotency::class, ['--days' => 7]);
    expect(DB::table('idempotency_keys')->count())->toBe(1);
});

it('prune:refresh-tokens idempotent', function () {
    $u = \App\Models\User::factory()->create();

    RefreshToken::create([
        'user_id' => $u->id,
        'token_hash' => str_repeat('1', 64),
        'revoked' => true,
        'expires_at' => now()->subDay(),
    ]);
    RefreshToken::create([
        'user_id' => $u->id,
        'token_hash' => str_repeat('2', 64),
        'revoked' => false,
        'expires_at' => now()->subMinute(),
    ]);
    RefreshToken::create([
        'user_id' => $u->id,
        'token_hash' => str_repeat('3', 64),
        'revoked' => false,
        'expires_at' => now()->addDays(10),
    ]);

    Artisan::call(PruneRefreshTokens::class);
    $left = RefreshToken::count();
    expect($left)->toBe(1);

    Artisan::call(PruneRefreshTokens::class);
    expect(RefreshToken::count())->toBe(1);
});

it('cleanup:attachments orphan kaydı siler', function () {
    Storage::fake('public');

    $a = Attachment::create([
        'attachable_type' => \App\Models\WorkOrder::class,
        'attachable_id'   => 99999,
        'name' => 'missing.pdf',
        'path' => 'attachments/missing.pdf',
        'size' => 100,
        'mime' => 'application/pdf',
        'meta' => null,
    ]);

    Storage::disk('public')->assertMissing($a->path);

    Artisan::call(CleanOrphanAttachments::class);
    expect(Attachment::find($a->id))->toBeNull();

    Artisan::call(CleanOrphanAttachments::class);
    expect(Attachment::where('id',$a->id)->exists())->toBeFalse();
});
