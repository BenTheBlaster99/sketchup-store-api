<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_creator')->default(false)->after('is_beta');
            $table->enum('creator_status', ['none', 'pending', 'approved', 'suspended'])
                ->default('none')
                ->after('is_creator');
            $table->string('display_name')->nullable()->after('creator_status');
            $table->text('bio')->nullable()->after('display_name');
            $table->string('paypal_email')->nullable()->after('bio');
            $table->unsignedTinyInteger('revenue_share_percentage')->default(40)->after('paypal_email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_creator',
                'creator_status',
                'display_name',
                'bio',
                'paypal_email',
                'revenue_share_percentage',
            ]);
        });
    }
};
