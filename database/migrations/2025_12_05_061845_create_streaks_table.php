<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('streaks', function (Blueprint $table) {
            $table->id();
            // Foreign key linking back to the users table
            $table->foreignId('user_id')->constrained()->unique(); 
            // The current active streak count
            $table->integer('current_streak')->default(0); 
            // The longest streak achieved
            $table->integer('longest_streak')->default(0); 
            // The last time a check-in occurred (for streak calculation)
            $table->timestamp('last_check_in')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('streaks');
    }
};