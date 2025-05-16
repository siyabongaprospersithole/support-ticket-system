<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->morphs('subject'); // Polymorphic relationship for the model being tracked
            $table->string('type'); // Type of activity (created, updated, commented, etc.)
            $table->text('description'); // Description of the activity
            $table->json('properties')->nullable(); // Store additional properties
            $table->foreignId('causer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('causer_type')->nullable(); // In case we want to track system actions
            $table->timestamps();
            
            // Only add indexes that aren't already created by morphs()
            $table->index(['causer_type', 'causer_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
}; 