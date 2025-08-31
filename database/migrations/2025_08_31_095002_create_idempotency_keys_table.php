<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('idempotency_keys', function (Blueprint $t) {
            $t->id();
            $t->string('key', 80)->unique();
            $t->string('fingerprint', 64);
            $t->unsignedSmallInteger('status')->nullable();
            $t->longText('body')->nullable();
            $t->timestamps();
            $t->index('created_at');
        });
    }
    public function down(): void { Schema::dropIfExists('idempotency_keys'); }
};
