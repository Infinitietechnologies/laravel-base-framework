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
        schema::table('merchants', function (Blueprint $table) {
            $table->renameColumn('visiblity_status', 'visibility_status');
            $table->string('verification_status')->nullable()->change();
            $table->string('visibility_status')->nullable()->change();
            $table->uuid('uuid')->unique()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        schema::table('merchants', function (Blueprint $table) {
           $table->renameColumn('visibility_status', 'visiblity_status');
        });
    }
};
