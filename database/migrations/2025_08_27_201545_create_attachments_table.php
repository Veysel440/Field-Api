<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attachments', function (Blueprint $t) {
            $t->id();
            $t->string('entity_type');
            $t->unsignedBigInteger('entity_id');
            $t->string('name');
            $t->string('path');
            $t->unsignedBigInteger('size');
            $t->string('mime');
            $t->timestamps();
            $t->index(['entity_type','entity_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('attachments'); }
};
