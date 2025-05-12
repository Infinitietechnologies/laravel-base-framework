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
        schema::table('countries', function (Blueprint $table) {
            $table->renameColumn('logitude', 'longitude');
            $table->renameColumn('phone_code', 'phonecode');
            $table->string('wikiDataId')->nullable()->change();
            $table->string('native')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        schema::table('countries', function (Blueprint $table) {
            $table->renameColumn('longitude', 'logitude');
            $table->renameColumn('phonecode', 'phone_code');
        });
    }
};
