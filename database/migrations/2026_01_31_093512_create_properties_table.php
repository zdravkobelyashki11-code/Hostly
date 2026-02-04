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

// My code starts here
Schema::create('properties', function (Blueprint $table) {
    $table->id();
    $table->foreignId('host_id')->constrained('users')->onDelete('cascade');
    $table->string('title');
    $table->text('description');
    $table->decimal('price_per_night', 10, 2);
    $table->string('location');
    $table->string('city');
    $table->string('country');
    $table->integer('max_guests')->default(1);
    $table->integer('bedrooms')->default(1);
    $table->integer('bathrooms')->default(1);
    $table->boolean('is_active')->default(true);
    $table->timestamps();

});
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
