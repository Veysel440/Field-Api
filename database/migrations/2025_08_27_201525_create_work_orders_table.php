<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('work_orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $t->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $t->string('code')->unique();
            $t->string('title');
            $t->enum('status', ['open','in_progress','done'])->default('open');
            $t->decimal('lat',10,7)->nullable();
            $t->decimal('lng',10,7)->nullable();
            $t->timestamps();
            $t->index(['lat','lng']);
        });
    }
    public function down(): void { Schema::dropIfExists('work_orders'); }
};
