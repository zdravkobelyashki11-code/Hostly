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
        Schema::create('property_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('property_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->json('sub_ratings')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['booking_id', 'reviewer_id']);
            $table->index('property_id');
        });

        Schema::create('user_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('reviewee_id')->constrained('users')->restrictOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->json('sub_ratings')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['booking_id', 'reviewer_id']);
            $table->index('reviewee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reviews');
        Schema::dropIfExists('property_reviews');
    }
};
