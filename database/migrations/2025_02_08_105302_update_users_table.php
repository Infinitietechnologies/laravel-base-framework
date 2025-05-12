<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile')->unique()->after('email');
            $table->string('referral_code')->nullable()->after('password');
            $table->string('friends_code')->nullable()->after('password');
            $table->decimal('reward_points',10,2)->default(0.00)->after('password');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
