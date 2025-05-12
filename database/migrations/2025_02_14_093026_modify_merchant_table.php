<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update datatype of mobile field in addresses table
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('mobile')->change();
        });

        // Drop mobile column in merchant table
        Schema::table('merchants', function (Blueprint $table) {
            $table->string('pricing_template_id')->nullable()->after('longitude')->change();
            $table->enum('verification_status', ['approved', 'not_approved','rejected'])->default('not_approved')->change();
            $table->dropColumn('mobile');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->enum('verification_status', ['approved', 'not_approved','rejected'])->default('not_approved')->change();
            $table->string('contact_number')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert changes made in the up method
        Schema::table('addresses', function (Blueprint $table) {
            $table->integer('mobile')->change();
        });
    }
};
