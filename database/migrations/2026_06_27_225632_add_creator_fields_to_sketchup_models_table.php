<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sketchup_models', function (Blueprint $table) {
            $table->foreignId('creator_id')
                ->nullable()
                ->after('category_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->enum('review_status', ['approved', 'pending_review', 'rejected'])
                ->default('approved')
                ->after('is_published');
            $table->text('rejection_note')->nullable()->after('review_status');
        });
    }

    public function down(): void
    {
        Schema::table('sketchup_models', function (Blueprint $table) {
            $table->dropConstrainedForeignId('creator_id');
            $table->dropColumn(['review_status', 'rejection_note']);
        });
    }
};
