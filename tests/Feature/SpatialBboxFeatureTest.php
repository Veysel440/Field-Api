<?php

use App\Models\Asset;
use Illuminate\Support\Facades\DB;

it('bbox ST_Contains ile doğru filtreler', function () {

    $istanbul = Asset::create([
        'code' => 'IST1', 'name'=>'Istanbul',
        'customer_id' => \App\Models\Customer::factory()->create()->id,
        'lat' => 41.01, 'lng' => 28.97,
    ]);

    $ankara = Asset::create([
        'code' => 'ANK1', 'name'=>'Ankara',
        'customer_id' => \App\Models\Customer::factory()->create()->id,
        'lat' => 39.93, 'lng' => 32.85,
    ]);

    $bboxIst = '28.0,40.5,30.0,41.5';
    $r1 = $this->getJson("/api/assets/map?bbox={$bboxIst}")
        ->assertOk()->json();

    expect($r1['total'])->toBe(1);
    expect(collect($r1['data'])->pluck('id')->all())->toContain($istanbul->id);

    $bboxAll = '27.0,38.0,34.0,42.0';
    $r2 = $this->getJson("/api/assets/map?bbox={$bboxAll}")
        ->assertOk()->json();

    expect($r2['total'])->toBe(2);
});
