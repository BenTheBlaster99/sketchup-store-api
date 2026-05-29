<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pack_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'active', 'rejected'])->default('pending');
            $table->unsignedInteger('price_paid_dzd');
            $table->string('payment_ref')->nullable();
            $table->string('payer_name')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pack_purchases');
    }
};
