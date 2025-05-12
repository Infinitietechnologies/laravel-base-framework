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
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('slug',300)->unique();
            $table->string('address');
            $table->string('city',100);
            $table->string('state',100);
            $table->string('zipcode',20);
            $table->integer('mobile');
            $table->string('country',100);
            $table->string('country_code',100);
            $table->decimal('latitude',10,8)->nullable();
            $table->decimal('longitude',11,8)->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('pricing_template_id');
            $table->text('business_license');
            $table->text('authorized_signature');
            $table->text('articles_of_Incorporation');
            $table->text('national_identity_card');
            $table->enum('verification_status', ['approved', 'not_approved'])->default('not_approved');
            $table->enum('visiblity_status', ['visible', 'draft'])->default('draft');
            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
