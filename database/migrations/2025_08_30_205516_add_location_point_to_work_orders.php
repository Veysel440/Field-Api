<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $t) {
            if (!Schema::hasColumn('work_orders', 'lat')) {
                $t->decimal('lat', 10, 7)->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('work_orders', 'lng')) {
                $t->decimal('lng', 10, 7)->nullable()->after('lat');
            }
            $t->index(['lat','lng'], 'idx_work_orders_lat_lng');
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql' && !Schema::hasColumn('work_orders', 'location')) {
            DB::statement("ALTER TABLE `work_orders` ADD COLUMN `location` POINT SRID 4326 NULL AFTER `lng`");
            DB::statement("UPDATE `work_orders` SET `location` = ST_SRID(POINT(`lng`,`lat`),4326) WHERE `lat` IS NOT NULL AND `lng` IS NOT NULL");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('work_orders', function (Blueprint $t) {
            $t->dropIndex('idx_work_orders_lat_lng');
            if (Schema::hasColumn('work_orders', 'lat')) $t->dropColumn('lat');
            if (Schema::hasColumn('work_orders', 'lng')) $t->dropColumn('lng');
        });

        if ($driver === 'mysql' && Schema::hasColumn('work_orders', 'location')) {
            DB::statement("ALTER TABLE `work_orders` DROP COLUMN `location`");
        }
    }
};
