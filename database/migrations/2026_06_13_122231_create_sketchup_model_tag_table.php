<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sketchup_model_tag', function (Blueprint $table) {
            $table->foreignId('sketchup_model_id')->constrained('sketchup_models')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();

            $table->primary(['sketchup_model_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sketchup_model_tag');
    }
};
