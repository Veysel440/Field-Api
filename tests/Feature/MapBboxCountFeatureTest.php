<?php

use App\Models\{Asset, Customer, WorkOrder};
use Illuminate\Support\Facades\DB;

it('assets/map count gerçek satır sayısına eşit', function () {
    $c = Customer::factory()->create();

    Asset::factory()->count(3)->create(['customer_id'=>$c->id,'lat'=>41.05,'lng'=>29.05]);
    Asset::factory()->count(2)->create(['customer_id'=>$c->id,'lat'=>45.0,'lng'=>10.0]);

    DB::statement("UPDATE assets SET geom = ST_SRID(POINT(lng,lat), 4326) WHERE lat IS NOT NULL AND lng IS NOT NULL");

    $bbox = '29.0,41.0,29.2,41.2';
    $r = $this->getJson('/api/assets/map?bbox='.$bbox)->assertOk()->json();
    expect($r['count'])->toBe(3);
    expect(count($r['data']))->toBe(3);
});
