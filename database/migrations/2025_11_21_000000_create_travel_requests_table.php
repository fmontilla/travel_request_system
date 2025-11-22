<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('requester_name');
            $table->string('destination');
            $table->dateTime('departure_date');
            $table->dateTime('return_date');
            $table->enum('status', ['requested', 'approved', 'cancelled'])->default('requested');
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('departure_date');
            $table->index('return_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_requests');
    }
};
