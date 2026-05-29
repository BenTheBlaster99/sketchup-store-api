<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sketchup_model_id')->constrained('sketchup_models')->cascadeOnDelete();
            $table->string('ip_address')->nullable();
            $table->timestamp('delivered_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
