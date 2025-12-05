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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            
            // Core goal details
            $table->string('title');
            $table->text('description')->nullable();
            
            // Progress tracking - using decimal for flexibility
            $table->decimal('target_value', 10, 2)->default(1.00);
            $table->decimal('current_value', 10, 2)->default(0.00);
            $table->string('unit', 50)->default('count'); // days, hours, reps, etc.
            
            // Organization
            $table->string('category', 100)->nullable()->index();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])
                ->default('medium')
                ->index();
            
            // Status tracking
            $table->enum('status', ['active', 'completed', 'archived', 'paused'])
                ->default('active')
                ->index();
            
            // Timestamps
            $table->timestamp('completed_at')->nullable();
            $table->date('deadline')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'category']);
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};