<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('email');
            $table->boolean('is_student')->default(false)->after('is_admin');
            $table->boolean('is_beta')->default(false)->after('is_student');
            $table->string('hardware_id')->nullable()->after('is_beta');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'is_student', 'is_beta', 'hardware_id']);
        });
    }
};
