<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('checklist_templates')) {
            Schema::create('checklist_templates', function (Blueprint $t) {
                $t->id();
                $t->string('title');
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('checklist_template_items')) {
            Schema::create('checklist_template_items', function (Blueprint $t) {
                $t->id();
                $t->foreignId('checklist_template_id')
                    ->constrained('checklist_templates')->cascadeOnDelete();
                $t->string('title');
                $t->unsignedInteger('sort')->default(0);
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('work_order_checklist_items')) {
            Schema::create('work_order_checklist_items', function (Blueprint $t) {
                $t->id();
                $t->foreignId('work_order_id')
                    ->constrained('work_orders')->cascadeOnDelete();
                $t->string('title');
                $t->boolean('done')->default(false);
                $t->unsignedInteger('sort')->default(0);
                $t->timestamps();
                $t->unique(['work_order_id','title']);
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('work_order_checklist_items');
        Schema::dropIfExists('checklist_template_items');
        Schema::dropIfExists('checklist_templates');
    }
};
