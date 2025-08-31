<?php

use App\Models\{Customer, Asset, WorkOrder};
use App\Support\CacheVersion;

it('AssetObserver cache versiyonu artırır', function () {
    $v0 = CacheVersion::get('assets');
    $a = Asset::factory()->create(['customer_id'=>Customer::factory()->create()->id]);
    $v1 = CacheVersion::get('assets');
    expect($v1)->toBeGreaterThan($v0);

    $a->update(['name'=>'X']);
    $v2 = CacheVersion::get('assets');
    expect($v2)->toBeGreaterThan($v1);

    $a->delete();
    $v3 = CacheVersion::get('assets');
    expect($v3)->toBeGreaterThan($v2);
});

it('WorkOrderObserver cache versiyonu artırır', function () {
    $v0 = CacheVersion::get('work_orders');
    $w = WorkOrder::factory()->create();
    $v1 = CacheVersion::get('work_orders');
    expect($v1)->toBeGreaterThan($v0);

    $w->update(['title'=>'Y']);
    $v2 = CacheVersion::get('work_orders');
    expect($v2)->toBeGreaterThan($v1);
});
