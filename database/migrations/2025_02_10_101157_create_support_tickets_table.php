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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_type_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('ticket_type_id')->references('id')->on('support_ticket_types');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('subject');
            $table->string('slug')->nullable();
            $table->string('email');
            $table->text('description');
            $table->enum('status', ['open', 'in_progress', 'reopen', 'pending_review', 'resolved', 'closed'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
