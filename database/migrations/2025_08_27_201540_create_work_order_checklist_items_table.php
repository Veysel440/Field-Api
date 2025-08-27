<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('work_order_checklist_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->boolean('done')->default(false);
            $t->unsignedInteger('ord')->default(0);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('work_order_checklist_items'); }
};
