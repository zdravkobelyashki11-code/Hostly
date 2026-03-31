<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('street_address')->nullable()->after('price_per_night');
        });

        DB::table('properties')
            ->whereNull('street_address')
            ->update([
                'street_address' => DB::raw('location'),
            ]);

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('host_id')->nullable()->after('guest_id')->constrained('users')->restrictOnDelete();
            $table->index('host_id');
        });

        $this->backfillBookingHosts();

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('location');
        });

        Schema::table('property_reviews', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->dropIndex(['property_id']);
            $table->dropColumn('property_id');
        });

        Schema::table('user_reviews', function (Blueprint $table) {
            $table->dropForeign(['reviewee_id']);
            $table->dropIndex(['reviewee_id']);
            $table->dropColumn('reviewee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('location')->nullable()->after('price_per_night');
        });

        DB::table('properties')
            ->whereNull('location')
            ->update([
                'location' => DB::raw('street_address'),
            ]);

        Schema::table('property_reviews', function (Blueprint $table) {
            $table->foreignId('property_id')->nullable()->after('reviewer_id')->constrained()->restrictOnDelete();
            $table->index('property_id');
        });

        DB::table('property_reviews')
            ->join('bookings', 'bookings.id', '=', 'property_reviews.booking_id')
            ->update([
                'property_reviews.property_id' => DB::raw('bookings.property_id'),
            ]);

        Schema::table('user_reviews', function (Blueprint $table) {
            $table->foreignId('reviewee_id')->nullable()->after('reviewer_id')->constrained('users')->restrictOnDelete();
            $table->index('reviewee_id');
        });

        $this->backfillUserReviewReviewees();

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['host_id']);
            $table->dropIndex(['host_id']);
            $table->dropColumn('host_id');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('street_address');
        });
    }

    private function backfillBookingHosts(): void
    {
        DB::table('bookings')
            ->select('bookings.id', 'properties.host_id')
            ->join('properties', 'properties.id', '=', 'bookings.property_id')
            ->orderBy('bookings.id')
            ->chunk(100, function ($bookings): void {
                foreach ($bookings as $booking) {
                    DB::table('bookings')
                        ->where('id', $booking->id)
                        ->update(['host_id' => $booking->host_id]);
                }
            });
    }

    private function backfillUserReviewReviewees(): void
    {
        DB::table('user_reviews')
            ->select('user_reviews.id', 'user_reviews.reviewer_id', 'bookings.guest_id', 'bookings.host_id')
            ->join('bookings', 'bookings.id', '=', 'user_reviews.booking_id')
            ->orderBy('user_reviews.id')
            ->chunk(100, function ($reviews): void {
                foreach ($reviews as $review) {
                    $revieweeId = null;

                    if ((int) $review->reviewer_id === (int) $review->guest_id) {
                        $revieweeId = $review->host_id;
                    } elseif ((int) $review->reviewer_id === (int) $review->host_id) {
                        $revieweeId = $review->guest_id;
                    }

                    DB::table('user_reviews')
                        ->where('id', $review->id)
                        ->update(['reviewee_id' => $revieweeId]);
                }
            });
    }
};
