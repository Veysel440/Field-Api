<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('work_orders', function(Blueprint $t){
            $t->decimal('lat',10,7)->nullable()->after('customer_id');
            $t->decimal('lng',10,7)->nullable()->after('lat');
            $t->point('location',4326)->nullable()->after('lng');
            $t->spatialIndex('location');
        });
        DB::statement("UPDATE work_orders SET location = ST_SRID(POINT(lng,lat),4326) WHERE lat IS NOT NULL AND lng IS NOT NULL");
    }
    public function down(): void {
        Schema::table('work_orders', function(Blueprint $t){
            $t->dropSpatialIndex(['location']);
            $t->dropColumn(['location','lat','lng']);
        });
    }
};
